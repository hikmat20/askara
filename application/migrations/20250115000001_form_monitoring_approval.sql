-- =============================================================================
-- Migration: form_monitoring_approval
-- Date: 2025-01-15
-- Description: Tambah kolom PIC (reviewer/approver), kolom audit trail review/approval,
--              update view_forms, dan buat tabel form_status_logs
-- Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 2.1
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Task 1.1: Tambah kolom reviewer_position_id dan approver_position_id ke forms
-- Requirements: 1.1, 1.2, 1.3
-- -----------------------------------------------------------------------------

ALTER TABLE `forms`
    ADD COLUMN IF NOT EXISTS `reviewer_position_id` INT(11) NULL DEFAULT NULL COMMENT 'FK ke positions.id — jabatan PIC Reviewer',
    ADD COLUMN IF NOT EXISTS `approver_position_id` INT(11) NULL DEFAULT NULL COMMENT 'FK ke positions.id — jabatan PIC Approver';

-- Foreign key constraints (opsional, tambahkan jika engine InnoDB dan belum ada)
-- ALTER TABLE `forms`
--     ADD CONSTRAINT `fk_forms_reviewer_position`
--         FOREIGN KEY (`reviewer_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
--     ADD CONSTRAINT `fk_forms_approver_position`
--         FOREIGN KEY (`approver_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- -----------------------------------------------------------------------------
-- Task 1.2: Tambah kolom reviewed_by, reviewed_at, approved_by, approved_at
-- Requirements: 1.4, 1.5, 1.6, 1.7
-- -----------------------------------------------------------------------------

ALTER TABLE `forms`
    ADD COLUMN IF NOT EXISTS `reviewed_by`  INT(11)  NULL DEFAULT NULL COMMENT 'user_id yang melakukan review',
    ADD COLUMN IF NOT EXISTS `reviewed_at`  DATETIME NULL DEFAULT NULL COMMENT 'waktu review dilakukan',
    ADD COLUMN IF NOT EXISTS `approved_by`  INT(11)  NULL DEFAULT NULL COMMENT 'user_id yang melakukan approval',
    ADD COLUMN IF NOT EXISTS `approved_at`  DATETIME NULL DEFAULT NULL COMMENT 'waktu approval dilakukan';

-- -----------------------------------------------------------------------------
-- Task 1.3: Update view_forms — tambah reviewer_position_name & approver_position_name
-- Requirements: 1.8
-- Catatan: Definisi SELECT di bawah mencakup semua kolom yang sudah ada di forms
--          ditambah join ke positions dua kali dengan alias berbeda.
--          Sesuaikan kolom SELECT jika ada kolom tambahan di tabel forms.
-- -----------------------------------------------------------------------------

CREATE OR REPLACE VIEW `view_forms` AS
SELECT
    f.id,
    f.company_id,
    f.departement_id,
    f.procedure_id,
    f.name,
    f.number,
    f.revision_number,
    f.issue_date,
    f.effective_date,
    f.published_date,
    f.is_active,
    f.file_name,
    f.size,
    f.ext,
    f.link_form,
    f.status,
    f.note,
    f.reviewer_position_id,
    f.approver_position_id,
    f.reviewed_by,
    f.reviewed_at,
    f.approved_by,
    f.approved_at,
    f.created_by,
    f.created_at,
    f.modified_by,
    f.modified_at,
    -- Nama jabatan reviewer
    pos_rev.name  AS reviewer_position_name,
    -- Nama jabatan approver
    pos_apv.name  AS approver_position_name,
    -- Nama departemen (join ke departements jika ada)
    d.name        AS departement_name
FROM `forms` f
LEFT JOIN `positions`    pos_rev ON pos_rev.id = f.reviewer_position_id
LEFT JOIN `positions`    pos_apv ON pos_apv.id = f.approver_position_id
LEFT JOIN `departements` d       ON d.id        = f.departement_id;

-- -----------------------------------------------------------------------------
-- Task 1.4: Buat tabel form_status_logs
-- Requirements: 2.1
-- -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `form_status_logs` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `form_id`    INT(11)      NOT NULL                 COMMENT 'FK ke forms.id',
    `old_status` VARCHAR(10)  NOT NULL                 COMMENT 'Status sebelum perubahan',
    `new_status` VARCHAR(10)  NOT NULL                 COMMENT 'Status setelah perubahan',
    `action_by`  INT(11)      NOT NULL                 COMMENT 'user_id yang melakukan aksi',
    `action_at`  DATETIME     NOT NULL                 COMMENT 'Waktu aksi dilakukan',
    `note`       TEXT         NULL DEFAULT NULL        COMMENT 'Catatan opsional (wajib diisi saat COR)',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_form_status_logs_form_id` (`form_id`),
    INDEX `idx_form_status_logs_action_at` (`action_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log riwayat perubahan status Form untuk audit trail';

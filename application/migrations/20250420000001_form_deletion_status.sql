-- =============================================================================
-- Migration: form_deletion_status
-- Date: 2025-04-20
-- Description: Tambah kolom deletion_status ke tabel forms dan update view_forms
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Tambah kolom deletion_status ke tabel forms
-- -----------------------------------------------------------------------------

ALTER TABLE `forms`
    ADD COLUMN IF NOT EXISTS `deletion_status` VARCHAR(10) NULL DEFAULT NULL
        COMMENT 'Sub-status proses deletion: OPN, REV, APV, DEL, REJ';

-- -----------------------------------------------------------------------------
-- Update view_forms — tambah kolom deletion_status
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
    f.deletion_status,
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
    -- Nama departemen
    d.name        AS departement_name
FROM `forms` f
LEFT JOIN `positions`    pos_rev ON pos_rev.id = f.reviewer_position_id
LEFT JOIN `positions`    pos_apv ON pos_apv.id = f.approver_position_id
LEFT JOIN `departements` d       ON d.id        = f.departement_id;

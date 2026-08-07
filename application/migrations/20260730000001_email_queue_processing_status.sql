-- =============================================================================
-- Migration: email_queue_processing_status
-- Date: 2026-07-30
-- Description: Penambahan status 'PRG' (Processing) dan indeks kolom status
--              pada tabel `email_queues` untuk pencegahan race condition.
-- =============================================================================

-- 1. Buat Tabel email_queues jika Belum Ada
CREATE TABLE IF NOT EXISTS `email_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT 1,
  `to_email` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `action_url` text DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'PND' COMMENT 'PND=Pending, PRG=Processing, SND=Sent, FAI=Failed',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error_msg` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Perbarui Kolom status agar Mendukung Status 'PRG' (Processing)
ALTER TABLE `email_queues` MODIFY COLUMN `status` varchar(10) NOT NULL DEFAULT 'PND' COMMENT 'PND=Pending, PRG=Processing, SND=Sent, FAI=Failed';

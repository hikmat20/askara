-- =============================================================================
-- Migration: email_configurations
-- Date: 2026-07-22
-- Description: Master list konfigurasi email server/sender, provider presets,
--              status aktif, dan log pengujian SMTP.
--              Termasuk skrip auto-migrate data dari tabel settings.
-- =============================================================================

-- 1. Buat Tabel Master Konfigurasi Email Server
CREATE TABLE IF NOT EXISTS `email_configurations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT 'Nama Identitas Konfigurasi',
  `provider` varchar(50) NOT NULL DEFAULT 'custom' COMMENT 'gmail, brevo, hostinger, mailgun, amazon_ses, smtp2go, microsoft365, custom',
  `smtp_host` varchar(255) NOT NULL,
  `smtp_port` int(5) NOT NULL DEFAULT '465',
  `smtp_user` varchar(255) NOT NULL,
  `smtp_pass` text NOT NULL COMMENT 'Encrypted password',
  `smtp_crypto` enum('ssl','tls','none') NOT NULL DEFAULT 'ssl',
  `sender_name` varchar(150) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `reply_to_name` varchar(150) DEFAULT NULL,
  `reply_to_email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `last_test_at` datetime DEFAULT NULL,
  `last_test_status` enum('success','failed') DEFAULT NULL,
  `last_success_at` datetime DEFAULT NULL,
  `last_error_at` datetime DEFAULT NULL,
  `last_error_msg` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Migrasikan Konfigurasi SMTP Existing dari Tabel `settings` ke `email_configurations` (Jika Belum Ada Data)
INSERT INTO `email_configurations` (
  `title`,
  `provider`,
  `smtp_host`,
  `smtp_port`,
  `smtp_user`,
  `smtp_pass`,
  `smtp_crypto`,
  `sender_name`,
  `sender_email`,
  `is_active`,
  `created_at`
)
SELECT
  'Konfigurasi Utama (Migrasi)' AS `title`,
  IF(MAX(CASE WHEN setting_name = 'smtp_host' THEN value END) LIKE '%gmail%', 'gmail', 'custom') AS `provider`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_host' THEN value END), 'ssl://smtp.googlemail.com') AS `smtp_host`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_port' THEN value END), '465') AS `smtp_port`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_user' THEN value END), '') AS `smtp_user`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_pass' THEN value END), '') AS `smtp_pass`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_crypto' THEN value END), 'ssl') AS `smtp_crypto`,
  'Askara Notification System' AS `sender_name`,
  COALESCE(MAX(CASE WHEN setting_name = 'smtp_user' THEN value END), '') AS `sender_email`,
  1 AS `is_active`,
  NOW() AS `created_at`
FROM `settings`
WHERE `setting_name` LIKE 'smtp_%'
  AND NOT EXISTS (SELECT 1 FROM `email_configurations`)
HAVING `smtp_user` != '';

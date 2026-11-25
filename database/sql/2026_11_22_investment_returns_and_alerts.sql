-- =====================================================================
-- Investment returns + credit/debit alerts: database changes
-- For servers where `php artisan migrate` can't be run.
--
-- HOW TO USE (phpMyAdmin):
--   1. BACK UP FIRST: select the database -> Export -> Go.
--   2. Select the database -> SQL tab -> paste this whole file -> Go.
--   3. Run it ONCE. Running it a second time stops at step 3 with
--      "Duplicate column name" (nothing is damaged; it's already applied).
--
-- It does NOT delete or overwrite any existing data. Existing investments
-- keep their amounts and start earning daily returns from today.
-- Equivalent to migrations 2026_11_22_000001 .. 000004.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 1. Laravel's queue and cache tables (alert emails are queued; the
--    scheduler uses cache locks). Skipped if they already exist.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 2. NEW TABLE: transactions (ledger of every balance change)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `category` varchar(40) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `transactions_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 3. UPDATED TABLE: investments (10 new columns for daily returns)
-- ---------------------------------------------------------------------
ALTER TABLE `investments`
  ADD COLUMN `status` varchar(20) NOT NULL DEFAULT 'active' AFTER `amount`,
  ADD COLUMN `roi` decimal(5,2) DEFAULT NULL AFTER `status`,
  ADD COLUMN `duration_days` int(10) unsigned DEFAULT NULL AFTER `roi`,
  ADD COLUMN `daily_return` decimal(12,2) DEFAULT NULL AFTER `duration_days`,
  ADD COLUMN `starts_at` timestamp NULL DEFAULT NULL AFTER `daily_return`,
  ADD COLUMN `ends_at` timestamp NULL DEFAULT NULL AFTER `starts_at`,
  ADD COLUMN `days_paid` int(10) unsigned NOT NULL DEFAULT 0 AFTER `ends_at`,
  ADD COLUMN `total_earned` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `days_paid`,
  ADD COLUMN `last_paid_at` timestamp NULL DEFAULT NULL AFTER `total_earned`,
  ADD COLUMN `completed_at` timestamp NULL DEFAULT NULL AFTER `last_paid_at`,
  ADD INDEX `investments_status_index` (`status`);

-- Fill in existing investments from their plan; they start earning from today (UTC).
UPDATE `investments` i
JOIN `investment_plans` p ON p.`id` = i.`investment_plan_id`
SET i.`status` = 'active',
    i.`roi` = p.`roi`,
    i.`duration_days` = p.`duration`,
    i.`daily_return` = ROUND(i.`amount` * p.`roi` / 100, 2),
    i.`starts_at` = UTC_TIMESTAMP(),
    i.`ends_at` = UTC_TIMESTAMP() + INTERVAL p.`duration` DAY
WHERE i.`starts_at` IS NULL;


-- ---------------------------------------------------------------------
-- 4. NEW TABLE: investment_payouts (one row per paid day; the unique
--    key makes paying the same day twice impossible)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `investment_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `investment_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `day_number` int(10) unsigned NOT NULL,
  `payout_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `investment_payouts_investment_id_day_number_unique` (`investment_id`,`day_number`),
  KEY `investment_payouts_user_id_foreign` (`user_id`),
  KEY `investment_payouts_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `investment_payouts_investment_id_foreign` FOREIGN KEY (`investment_id`) REFERENCES `investments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `investment_payouts_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `investment_payouts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 5. NEW TABLE: settings (payout time, on/off, timezone, last run)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 6. Tell Laravel these migrations are done, so a future
--    `php artisan migrate` won't try to run them again.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @batch := (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.name, @batch FROM (
  SELECT '2026_11_22_000001_create_transactions_table' AS name
  UNION ALL SELECT '2026_11_22_000002_add_return_tracking_to_investments_table'
  UNION ALL SELECT '2026_11_22_000003_create_investment_payouts_table'
  UNION ALL SELECT '2026_11_22_000004_create_settings_table'
) m
WHERE NOT EXISTS (SELECT 1 FROM `migrations` x WHERE x.`migration` = m.name);

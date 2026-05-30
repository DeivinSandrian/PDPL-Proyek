-- Keep imported databases compatible with the current Express notification feature.
-- Older imports may have notifications.admin_id as NOT NULL, while the current app inserts message-only notifications.

ALTER TABLE notifications ADD COLUMN IF NOT EXISTS admin_id BIGINT UNSIGNED NULL AFTER id;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS message TEXT NULL AFTER admin_id;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS read_at TIMESTAMP NULL DEFAULT NULL AFTER message;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER read_at;
ALTER TABLE notifications MODIFY COLUMN admin_id BIGINT UNSIGNED NULL;
ALTER TABLE notifications MODIFY COLUMN message TEXT NULL;
ALTER TABLE notifications MODIFY COLUMN created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP;

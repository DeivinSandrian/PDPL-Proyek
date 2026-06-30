-- Keep imported databases compatible with the current Express notification feature.
-- Older imports may have notifications.admin_id as NOT NULL, while the current app inserts message-only notifications.
--
-- ADD COLUMN IF NOT EXISTS is not supported before MySQL 8.0.20, so we use stored
-- procedures that check INFORMATION_SCHEMA.COLUMNS before issuing each ALTER TABLE.
-- MODIFY COLUMN is always safe to re-run and needs no guard.

DROP PROCEDURE IF EXISTS _migrate_003_add_col;

CREATE PROCEDURE _migrate_003_add_col(
    IN p_column    VARCHAR(64),
    IN p_ddl       TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = 'notifications'
          AND COLUMN_NAME  = p_column
    ) THEN
        SET @_sql = p_ddl;
        PREPARE _stmt FROM @_sql;
        EXECUTE _stmt;
        DEALLOCATE PREPARE _stmt;
    END IF;
END;

CALL _migrate_003_add_col('admin_id',   'ALTER TABLE notifications ADD COLUMN admin_id   BIGINT UNSIGNED NULL          AFTER id');
CALL _migrate_003_add_col('message',    'ALTER TABLE notifications ADD COLUMN message    TEXT            NULL          AFTER admin_id');
CALL _migrate_003_add_col('read_at',    'ALTER TABLE notifications ADD COLUMN read_at    TIMESTAMP       NULL DEFAULT NULL AFTER message');
CALL _migrate_003_add_col('created_at', 'ALTER TABLE notifications ADD COLUMN created_at DATETIME        NULL DEFAULT CURRENT_TIMESTAMP AFTER read_at');

DROP PROCEDURE IF EXISTS _migrate_003_add_col;

-- Ensure nullability is correct regardless of how the columns were originally created.
ALTER TABLE notifications MODIFY COLUMN admin_id   BIGINT UNSIGNED NULL;
ALTER TABLE notifications MODIFY COLUMN message    TEXT            NULL;
ALTER TABLE notifications MODIFY COLUMN created_at DATETIME        NULL DEFAULT CURRENT_TIMESTAMP;

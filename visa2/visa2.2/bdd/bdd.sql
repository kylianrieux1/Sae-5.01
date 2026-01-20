-- Disable foreign key checks during import
SET FOREIGN_KEY_CHECKS=0;

START TRANSACTION;
COMMIT;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

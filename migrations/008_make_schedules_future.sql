-- Make all seeded schedules occur in the future relative to the current timestamp.
-- This ensures the dev environment has active schedules to display and integration tests run fully.

UPDATE schedules 
SET departure_time = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 2 DAY),
    arrival_estimate = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 51 HOUR)
WHERE schedule_id = 1;

UPDATE schedules 
SET departure_time = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 3 DAY),
    arrival_estimate = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 74 HOUR)
WHERE schedule_id = 6;

UPDATE schedules 
SET departure_time = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 4 DAY),
    arrival_estimate = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 105 HOUR)
WHERE schedule_id = 8;

UPDATE schedules 
SET departure_time = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 5 DAY),
    arrival_estimate = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 125 HOUR)
WHERE schedule_id = 9;

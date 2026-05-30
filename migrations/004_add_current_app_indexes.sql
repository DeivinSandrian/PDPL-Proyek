-- Add indexes expected by the current source-of-truth schema when migrating older imported databases.
-- MariaDB/XAMPP supports IF NOT EXISTS for index creation.

ALTER TABLE bookings ADD INDEX IF NOT EXISTS bookings_status_hold_index (status, hold_expired_at);
ALTER TABLE seats ADD UNIQUE KEY IF NOT EXISTS seats_vehicle_number_unique (vehicle_id, seat_number);
ALTER TABLE booking_seats ADD UNIQUE KEY IF NOT EXISTS booking_seats_booking_seat_unique (booking_id, seat_id);
ALTER TABLE payments ADD INDEX IF NOT EXISTS payments_gateway_transaction_id_index (gateway_transaction_id);
ALTER TABLE e_tickets ADD UNIQUE KEY IF NOT EXISTS e_tickets_booking_id_unique (booking_id);

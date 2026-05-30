-- Initial TravelGo schema for the current Node/Express app.
-- This migration is safe to run on an existing imported database because every table uses IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email),
    UNIQUE KEY users_username_unique (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS routes (
    route_id INT NOT NULL AUTO_INCREMENT,
    origin_city VARCHAR(100) NOT NULL,
    destination_city VARCHAR(100) NOT NULL,
    distance_km INT NULL,
    duration TIME NULL,
    PRIMARY KEY (route_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vehicles (
    vehicle_id INT NOT NULL AUTO_INCREMENT,
    plate_number VARCHAR(20) NOT NULL,
    vehicle_type VARCHAR(50) NULL,
    capacity INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    PRIMARY KEY (vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stop_points (
    stop_point_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NULL,
    type ENUM('pickup','dropoff') NOT NULL,
    PRIMARY KEY (stop_point_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS schedules (
    schedule_id INT NOT NULL AUTO_INCREMENT,
    route_id INT NULL,
    vehicle_id INT NULL,
    departure_time DATETIME NOT NULL,
    arrival_estimate DATETIME NULL,
    price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'available',
    pickup_point VARCHAR(255) NULL,
    dropoff_point VARCHAR(255) NULL,
    PRIMARY KEY (schedule_id),
    KEY schedules_route_id_index (route_id),
    KEY schedules_vehicle_id_index (vehicle_id),
    CONSTRAINT schedules_route_id_foreign FOREIGN KEY (route_id) REFERENCES routes(route_id),
    CONSTRAINT schedules_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seats (
    seat_id INT NOT NULL AUTO_INCREMENT,
    vehicle_id INT NULL,
    seat_number VARCHAR(10) NULL,
    seat_class VARCHAR(20) NULL,
    PRIMARY KEY (seat_id),
    UNIQUE KEY seats_vehicle_number_unique (vehicle_id, seat_number),
    KEY seats_vehicle_id_index (vehicle_id),
    CONSTRAINT seats_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    schedule_id INT NULL,
    booking_code VARCHAR(10) NOT NULL,
    booking_channel ENUM('online','offline') NOT NULL,
    total_amount DECIMAL(10,2) NULL,
    status ENUM('pending','confirmed','cancelled','expired') NOT NULL DEFAULT 'pending',
    hold_expired_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    passenger_name VARCHAR(255) NULL,
    PRIMARY KEY (booking_id),
    UNIQUE KEY bookings_booking_code_unique (booking_code),
    KEY bookings_user_id_index (user_id),
    KEY bookings_schedule_id_index (schedule_id),
    KEY bookings_status_hold_index (status, hold_expired_at),
    CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT bookings_schedule_id_foreign FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_seats (
    booking_seat_id INT NOT NULL AUTO_INCREMENT,
    booking_id INT NULL,
    seat_id INT NULL,
    price_at_booking DECIMAL(10,2) NULL,
    PRIMARY KEY (booking_seat_id),
    UNIQUE KEY booking_seats_booking_seat_unique (booking_id, seat_id),
    KEY booking_seats_booking_id_index (booking_id),
    KEY booking_seats_seat_id_index (seat_id),
    CONSTRAINT booking_seats_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    CONSTRAINT booking_seats_seat_id_foreign FOREIGN KEY (seat_id) REFERENCES seats(seat_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS passengers (
    passenger_id INT NOT NULL AUTO_INCREMENT,
    booking_id INT NULL,
    full_name VARCHAR(100) NULL,
    identity_number VARCHAR(50) NULL,
    phone VARCHAR(20) NULL,
    PRIMARY KEY (passenger_id),
    KEY passengers_booking_id_index (booking_id),
    CONSTRAINT passengers_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    payment_id INT NOT NULL AUTO_INCREMENT,
    booking_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50) NULL,
    status VARCHAR(20) NULL,
    gateway_transaction_id VARCHAR(100) NULL,
    paid_at DATETIME NULL,
    PRIMARY KEY (payment_id),
    KEY payments_booking_id_index (booking_id),
    KEY payments_gateway_transaction_id_index (gateway_transaction_id),
    CONSTRAINT payments_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS e_tickets (
    ticket_id INT NOT NULL AUTO_INCREMENT,
    booking_id INT NULL,
    ticket_code VARCHAR(20) NULL,
    qr_code TEXT NULL,
    issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ticket_id),
    UNIQUE KEY e_tickets_ticket_code_unique (ticket_code),
    UNIQUE KEY e_tickets_booking_id_unique (booking_id),
    CONSTRAINT e_tickets_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id BIGINT UNSIGNED NULL,
    message TEXT NULL,
    read_at TIMESTAMP NULL DEFAULT NULL,
    created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY notifications_admin_id_index (admin_id),
    CONSTRAINT notifications_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS user_data_etl
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE user_data_etl;

CREATE TABLE IF NOT EXISTS process_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    execution_date DATE NOT NULL,
    raw_file_name VARCHAR(255) NOT NULL,
    etl_file_name VARCHAR(255) NOT NULL,
    summary_file_name VARCHAR(255) NOT NULL,
    total_records INT NOT NULL,
    inserted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_header_id INT NOT NULL,
    external_user_id INT NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    gender VARCHAR(50),
    age INT,
    email VARCHAR(150),
    city VARCHAR(100),
    country VARCHAR(100),
    department VARCHAR(150),
    role VARCHAR(100),
    inserted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_detail_process
        FOREIGN KEY (process_header_id)
        REFERENCES process_header(id)
);

CREATE TABLE IF NOT EXISTS summary_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_header_id INT NOT NULL,
    metric VARCHAR(100) NOT NULL,
    metric_value VARCHAR(150) NOT NULL,
    total_count INT NOT NULL,
    inserted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_summary_item_process
        FOREIGN KEY (process_header_id)
        REFERENCES process_header(id)
);
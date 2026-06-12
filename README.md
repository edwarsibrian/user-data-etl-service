# User Data ETL Service

Symfony ETL service that extracts user data from an external API, transforms the dataset into CSV reports, persists processed information in MySQL, encrypts generated files, and exposes API endpoints to query execution results.

## Features

* Extracts user data from `https://dummyjson.com/users`
* Generates raw JSON file: `data_YYYYMMDD.json`
* Generates transformed CSV file: `ETL_YYYYMMDD.csv`
* Generates summary CSV file: `summary_YYYYMMDD.csv`
* Persists process header, detail data, and summary data in MySQL
* Encrypts generated files using AES-256-CBC with OpenSSL
* Exposes REST API endpoints for process monitoring
* Includes unit tests with PHPUnit
* Includes GitHub Actions CI pipeline

## Tech Stack

* PHP 8.2
* Symfony 7
* MySQL
* PDO
* OpenSSL
* PHPUnit
* GitHub Actions
* XAMPP-compatible environment

## Project Structure

```text
src/
├── Command/
│   └── RunUsersEtlCommand.php
├── Controller/
│   └── ApiProcessController.php
├── Repository/
│   └── ProcessRepository.php
└── Service/
    ├── EncryptionService.php
    └── SummaryGeneratorService.php

database/
└── schema.sql

storage/
├── json/
├── csv/
└── encrypted/

tests/
└── Service/
    ├── EncryptionServiceTest.php
    └── SummaryGeneratorServiceTest.php

.github/
└── workflows/
    └── ci.yml
```

## Environment Variables

Configuration is handled through the `.env` file.

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=user_data_etl
DB_USER=root
DB_PASSWORD=

ENCRYPTION_KEY=change-this-key-for-production
ENCRYPTION_METHOD=AES-256-CBC
```

## Installation

Clone the repository:

```bash
git clone https://github.com/edwarsibrian/user-data-etl-service.git
cd user-data-etl-service
```

Install dependencies:

```bash
composer install
```

## Database Setup

Create the MySQL database and tables using:

```bash
/ c/xampp/mysql/bin/mysql -u root < database/schema.sql
```

Or import `database/schema.sql` manually using phpMyAdmin.

## Running the ETL Process

Execute:

```bash
php bin/console app:run-users-etl
```

The command will:

1. Extract users from DummyJSON API
2. Save raw JSON file
3. Generate ETL CSV file
4. Generate summary CSV file
5. Persist results in MySQL
6. Encrypt generated files

Generated files are stored in:

```text
storage/json/
storage/csv/
storage/encrypted/
```

## API Endpoints

Start the local server:

```bash
php -S 127.0.0.1:8000 -t public
```

### Health Check

```http
GET /api/health
```

Response:

```json
{
  "status": "ok",
  "service": "user-data-etl-service"
}
```

### List ETL Executions

```http
GET /api/processes
```

### Get Summary by Process ID

```http
GET /api/processes/{id}/summary
```

## Running Tests

Execute:

```bash
php bin/phpunit
```

Example result:

```text
OK (2 tests, 7 assertions)
```

## Continuous Integration

GitHub Actions automatically runs PHPUnit tests on:

* Push
* Pull Request

Workflow file:

```text
.github/workflows/ci.yml
```

## Notes

* The project is designed to run in a XAMPP-compatible environment.
* Generated JSON, CSV, and encrypted files are excluded from source control.
* Encryption is implemented using native PHP OpenSSL functions.
* SFTP was not implemented because the assessment allowed file encryption as an alternative security requirement.
* Configuration values are externalized through environment variables using Symfony's `.env` mechanism.

```
```

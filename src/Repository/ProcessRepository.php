<?php

namespace App\Repository;

use PDO;

class ProcessRepository
{
    private PDO $connection;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dbname = $_ENV['DB_NAME'] ?? 'user_data_etl';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        $this->connection = new PDO($dsn, $user, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function saveProcess(
        string $executionDate,
        string $rawFileName,
        string $etlFileName,
        string $summaryFileName,
        array $users,
        array $summary
    ): int {
        $this->connection->beginTransaction();

        try {
            $headerStatement = $this->connection->prepare(
                'INSERT INTO process_header 
                (execution_date, raw_file_name, etl_file_name, summary_file_name, total_records)
                VALUES (:execution_date, :raw_file_name, :etl_file_name, :summary_file_name, :total_records)'
            );

            $headerStatement->execute([
                'execution_date' => $executionDate,
                'raw_file_name' => $rawFileName,
                'etl_file_name' => $etlFileName,
                'summary_file_name' => $summaryFileName,
                'total_records' => count($users),
            ]);

            $processHeaderId = (int) $this->connection->lastInsertId();

            $detailStatement = $this->connection->prepare(
                'INSERT INTO user_detail
                (process_header_id, external_user_id, first_name, last_name, gender, age, email, city, country, department, role)
                VALUES
                (:process_header_id, :external_user_id, :first_name, :last_name, :gender, :age, :email, :city, :country, :department, :role)'
            );

            foreach ($users as $user) {
                $detailStatement->execute([
                    'process_header_id' => $processHeaderId,
                    'external_user_id' => $user['id'] ?? 0,
                    'first_name' => $user['firstName'] ?? null,
                    'last_name' => $user['lastName'] ?? null,
                    'gender' => $user['gender'] ?? null,
                    'age' => $user['age'] ?? null,
                    'email' => $user['email'] ?? null,
                    'city' => $user['address']['city'] ?? null,
                    'country' => $user['address']['country'] ?? null,
                    'department' => $user['company']['department'] ?? null,
                    'role' => $user['role'] ?? null,
                ]);
            }

            $summaryStatement = $this->connection->prepare(
                'INSERT INTO summary_item
                (process_header_id, metric, metric_value, total_count)
                VALUES
                (:process_header_id, :metric, :metric_value, :total_count)'
            );

            foreach ($summary as $item) {
                $summaryStatement->execute([
                    'process_header_id' => $processHeaderId,
                    'metric' => $item['metric'],
                    'metric_value' => $item['value'],
                    'total_count' => $item['count'],
                ]);
            }

            $this->connection->commit();

            return $processHeaderId;
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    //Methods
    //--------
    public function getProcesses(): array
{
    $statement = $this->connection->query(
        'SELECT id, execution_date, raw_file_name, etl_file_name, summary_file_name, total_records, inserted_at
         FROM process_header
         ORDER BY id DESC'
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

public function getSummaryByProcessId(int $processId): array
{
    $statement = $this->connection->prepare(
        'SELECT metric, metric_value, total_count
         FROM summary_item
         WHERE process_header_id = :process_id
         ORDER BY metric, metric_value'
    );

    $statement->execute([
        'process_id' => $processId,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
}
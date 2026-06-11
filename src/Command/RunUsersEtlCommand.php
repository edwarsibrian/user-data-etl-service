<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:run-users-etl',
    description: 'Extracts users from DummyJSON API and generates a raw JSON file.',
)]
class RunUsersEtlCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = date('Ymd');

        $io->title('Starting Users ETL Process');

        try {
            $response = $this->httpClient->request('GET', 'https://dummyjson.com/users');
            $data = $response->toArray();

            if (!isset($data['users']) || !is_array($data['users'])) {
                $io->error('Invalid API response. The "users" array was not found.');
                return Command::FAILURE;
            }

            $jsonDirectory = __DIR__ . '/../../storage/json';
            $jsonPath = $jsonDirectory . '/data_' . $date . '.json';

            if (!is_dir($jsonDirectory)) {
                mkdir($jsonDirectory, 0777, true);
            }

            file_put_contents(
                $jsonPath,
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            $csvDirectory = __DIR__ . '/../../storage/csv';
$etlCsvPath = $csvDirectory . '/ETL_' . $date . '.csv';

if (!is_dir($csvDirectory)) {
    mkdir($csvDirectory, 0777, true);
}

$etlFile = fopen($etlCsvPath, 'w');

fputcsv($etlFile, [
    'id',
    'first_name',
    'last_name',
    'gender',
    'age',
    'email',
    'city',
    'country',
    'department',
    'role'
]);

foreach ($data['users'] as $user) {
    fputcsv($etlFile, [
        $user['id'] ?? '',
        $user['firstName'] ?? '',
        $user['lastName'] ?? '',
        $user['gender'] ?? '',
        $user['age'] ?? '',
        $user['email'] ?? '',
        $user['address']['city'] ?? '',
        $user['address']['country'] ?? '',
        $user['company']['department'] ?? '',
        $user['role'] ?? ''
    ]);
}

fclose($etlFile);

$io->writeln('ETL CSV file generated: ' . $etlCsvPath);

            $io->success('Raw JSON file generated successfully.');
            $io->writeln('File: ' . $jsonPath);
            $io->writeln('Users processed: ' . count($data['users']));

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $io->error('ETL process failed: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}
<?php

namespace App\Service;

class SummaryGeneratorService
{
    public function generate(array $users): array
    {
        $summary = [];

        foreach ($users as $user) {
            $items = [
                ['gender', $user['gender'] ?? 'unknown'],
                ['role', $user['role'] ?? 'unknown'],
                ['department', $user['company']['department'] ?? 'unknown'],
                ['city', $user['address']['city'] ?? 'unknown'],
            ];

            foreach ($items as [$metric, $value]) {
                $key = $metric . '|' . $value;

                if (!isset($summary[$key])) {
                    $summary[$key] = [
                        'metric' => $metric,
                        'value' => $value,
                        'count' => 0,
                    ];
                }

                $summary[$key]['count']++;
            }
        }

        return array_values($summary);
    }
}
<?php

namespace App\Tests\Service;

use App\Service\SummaryGeneratorService;
use PHPUnit\Framework\TestCase;

class SummaryGeneratorServiceTest extends TestCase
{
    public function testGenerateSummaryGroupsUsersByExpectedMetrics(): void
    {
        $service = new SummaryGeneratorService();

        $users = [
            [
                'gender' => 'female',
                'role' => 'admin',
                'company' => ['department' => 'Engineering'],
                'address' => ['city' => 'Phoenix'],
            ],
            [
                'gender' => 'female',
                'role' => 'user',
                'company' => ['department' => 'Engineering'],
                'address' => ['city' => 'Phoenix'],
            ],
            [
                'gender' => 'male',
                'role' => 'user',
                'company' => ['department' => 'Support'],
                'address' => ['city' => 'Houston'],
            ],
        ];

        $summary = $service->generate($users);

        $this->assertContains([
            'metric' => 'gender',
            'value' => 'female',
            'count' => 2,
        ], $summary);

        $this->assertContains([
            'metric' => 'gender',
            'value' => 'male',
            'count' => 1,
        ], $summary);

        $this->assertContains([
            'metric' => 'department',
            'value' => 'Engineering',
            'count' => 2,
        ], $summary);

        $this->assertContains([
            'metric' => 'city',
            'value' => 'Phoenix',
            'count' => 2,
        ], $summary);
    }
}
<?php

namespace App\Controller;

use App\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiProcessController
{
    public function __construct(
        private readonly ProcessRepository $processRepository
    ) {
    }

    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'service' => 'user-data-etl-service',
            'timestamp' => date('c'),
        ]);
    }

    #[Route('/api/processes', name: 'api_processes', methods: ['GET'])]
    public function processes(): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->processRepository->getProcesses(),
        ]);
    }

    #[Route('/api/processes/{id}/summary', name: 'api_process_summary', methods: ['GET'])]
    public function summary(int $id): JsonResponse
    {
        return new JsonResponse([
            'processId' => $id,
            'data' => $this->processRepository->getSummaryByProcessId($id),
        ]);
    }
}
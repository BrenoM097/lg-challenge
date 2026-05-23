<?php

namespace App\Services\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AiSimulationServiceInterface
{
    /**
     * Verifica se a chave da API Gemini está configurada.
     *
     * @return array{valid: bool, message?: string}
     */
    public function checkApiKey(): array;

    /**
     * Simula a produção e gera análise via IA Gemini.
     *
     * @param string $productLine
     * @param int $simulatedQuantity
     * @return array{response: string}
     */
    public function simulate(string $productLine, int $simulatedQuantity): array;
}

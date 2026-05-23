<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AiSimulationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiSimulationController extends Controller
{
    private $aiSimulationService;
    
    public function __construct(AiSimulationServiceInterface $aiSimulationService) {
        $this->aiSimulationService = $aiSimulationService;
    }

    /**
     * Verifica se a GEMINI_API_KEY está configurada no .env
     */
    public function checkApiKey(): JsonResponse
    {
        $result = $this->aiSimulationService->checkApiKey();

        return response()->json($result);
    }

    /**
     * Simula a produção e gera análise via IA Gemini.
     */
    public function simulate(Request $request): JsonResponse
    {
        $productLine = $request->input('product_line');
        $simulatedQuantity = $request->input('simulated_quantity');

        $result = $this->aiSimulationService->simulate($productLine, $simulatedQuantity);

        return response()->json($result);
    }
}
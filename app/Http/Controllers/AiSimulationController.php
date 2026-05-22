<?php

namespace App\Http\Controllers;

use App\Productivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiSimulationController extends Controller
{
    /**
     * Verifica se a GEMINI_API_KEY está configurada no .env
     */
    public function checkApiKey()
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            return response()->json([
                'valid' => false,
                'message' => 'Chave da API Gemini não configurada.'
            ]);
        }

        return response()->json(['valid' => true]);
    }

    public function simulate(Request $request)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'response' => '<span class="text-danger">Erro: Chave da API Gemini não configurada.</span>'
            ], 400);
        }

        $product = $request->input('product_line');
        $simulatedQuantity = $request->input('simulated_quantity');

        $history = Productivity::where('product_line', $product)->get();
        if ($history->isEmpty()) {
            return response()->json(['response' => 'Não há dados históricos suficientes para este produto.']);
        }

        $totalProduced = $history->sum('produced_quantity');
        $totalDefects = $history->sum('defect_count');
        $defectRate = ($totalDefects / $totalProduced) * 100;
        $expectedDefects = round(($defectRate / 100) * $simulatedQuantity);

        $prompt = $this->buildPrompt($product, $simulatedQuantity, $expectedDefects, $defectRate);

        return $this->sendToGemini($prompt);
    }


    private function buildPrompt($product, $quantity, $expectedDefects, $defectRate)
    {
        $behavior = "Você é um Consultor Sênior de Engenharia de Produção e Analista de Dados.";
        
        $context = "Simulação: Lote de {$quantity} unidades de {$product}. "
                 . "Taxa histórica de falha na nossa planta: " . round($defectRate, 2) . "%. "
                 . "Expectativa matemática: {$expectedDefects} produtos com defeito neste lote.";
                 
        $instruction = "Analise este cenário friamente e escreva um parecer em até 3 parágrafos curtos. "
                     . "Regras de avaliação que você deve seguir estritamente: "
                     . "1. Se a taxa for MENOR que 2.0%, considere um cenário excelente e sob controle. Elogie a estabilidade e foque apenas em manutenção preventiva. Não cite gargalos. "
                     . "2. Se a taxa estiver entre 2.0% e 5.0%, considere um cenário de alerta amarelo. Aponte risco de atrasos leves no setor de retrabalho. "
                     . "3. Apenas se a taxa for MAIOR que 5.0%, considere um cenário crítico e alerte sobre gargalos logísticos severos. "
                     . "Escreva o texto de forma direta, sem usar formatações especiais (como asteriscos ou negrito).";

        return "{$behavior}\n\n{$context}\n\n{$instruction}";
    }


    private function sendToGemini($prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        $aiModel  = config('services.gemini.model', 'gemini-flash-latest');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$aiModel}:generateContent?key={$apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                $status = $response->status();
                $errorMsg = $response->json()['error']['message'] ?? 'Erro desconhecido reportado pela API.';

                switch ($status) {
                    case 400:
                        return response()->json(['response' => "<span class='text-danger'>Erro 400 (Bad Request): A API rejeitou o formato ou não reconheceu o modelo ($model). Detalhe: $errorMsg</span>"]);
                    case 401:
                        return response()->json(['response' => "<span class='text-danger'>Erro 401 (Unauthorized): Sua Chave de API não é válida.</span>"]);
                    case 404:
                        return response()->json(['response' => "<span class='text-danger'>Erro 404 (Not Found): A URL ou o modelo não existem. Detalhe: $errorMsg</span>"]);
                    case 429:
                        return response()->json(['response' => "<span class='text-warning'>Erro 429 (Too Many Requests): Limite de requisições gratuitas esgotado. Tente mais tarde.</span>"]);
                    default:
                        return response()->json(['response' => "<span class='text-danger'>Erro de Rede ($status): $errorMsg</span>"]);
                }
            }

            $result = $response->json();
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Resposta vazia ou fora do formato esperado.';

            return response()->json(['response' => nl2br($aiText)]);

        } catch (\Exception $e) {
            return response()->json(['response' => "<span class='text-danger'>Erro crasso do servidor local: {$e->getMessage()}</span>"], 500);
        }
    }
}
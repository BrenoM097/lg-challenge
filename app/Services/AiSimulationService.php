<?php

namespace App\Services;

use App\Productivity;
use App\Services\Contracts\AiSimulationServiceInterface;
use Illuminate\Support\Facades\Http;

class AiSimulationService implements AiSimulationServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkApiKey(): array
    {
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return [
                'valid' => false,
                'message' => 'Chave da API Gemini não configurada.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function simulate(string $productLine, int $simulatedQuantity): array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return [
                'response' => '<span class="text-danger">Erro: Chave da API Gemini não configurada.</span>'
            ];
        }

        $history = Productivity::where('product_line', $productLine)->get();
        if ($history->isEmpty()) {
            return ['response' => 'Não há dados históricos suficientes para este produto.'];
        }

        $defectRate = $this->calculateDefectRate($history);
        $expectedDefects = $this->calculateExpectedDefects($defectRate, $simulatedQuantity);

        $prompt = $this->buildPrompt($productLine, $simulatedQuantity, $expectedDefects, $defectRate);

        return $this->sendToGemini($prompt);
    }

    /**
     * Calcula a taxa de defeitos a partir do histórico.
     *
     * @param \Illuminate\Database\Eloquent\Collection $history
     * @return float
     */
    private function calculateDefectRate($history): float
    {
        $totalProduced = $history->sum('produced_quantity');
        $totalDefects = $history->sum('defect_count');

        if ($totalProduced === 0) {
            return 0.0;
        }

        return ($totalDefects / $totalProduced) * 100;
    }

    /**
     * Calcula o número esperado de defeitos na simulação.
     *
     * @param float $defectRate
     * @param int $simulatedQuantity
     * @return int
     */
    private function calculateExpectedDefects(float $defectRate, int $simulatedQuantity): int
    {
        return round(($defectRate / 100) * $simulatedQuantity);
    }

    /**
     * Constrói o prompt para a IA conforme as regras de negócio.
     *
     * @param string $product
     * @param int $quantity
     * @param int $expectedDefects
     * @param float $defectRate
     * @return string
     */
    private function buildPrompt(string $product, int $quantity, int $expectedDefects, float $defectRate): string
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

    /**
     * Envia o prompt para a API Gemini e retorna a resposta.
     *
     * @param string $prompt
     * @return array{response: string}
     */
    private function sendToGemini(string $prompt): array
    {
        $apiKey = env('GEMINI_API_KEY');
        $aiModel = config('services.gemini.model', 'gemini-flash-latest');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$aiModel}:generateContent?key={$apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                return $this->handleApiError($response);
            }

            $result = $response->json();
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Resposta vazia ou fora do formato esperado.';

            return ['response' => nl2br($aiText)];

        } catch (\Exception $e) {
            return ['response' => "<span class='text-danger'>Erro do servidor local: {$e->getMessage()}</span>"];
        }
    }

    /**
     * Trata erros da API Gemini.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return array{response: string}
     */
    private function handleApiError($response): array
    {
        $status = $response->status();
        $errorMsg = $response->json()['error']['message'] ?? 'Erro desconhecido reportado pela API.';

        switch ($status) {
            case 400:
                return ['response' => "<span class='text-danger'>Erro 400 (Bad Request): A API rejeitou o formato ou não reconheceu o modelo. Detalhe: $errorMsg</span>"];
            case 401:
                return ['response' => "<span class='text-danger'>Erro 401 (Unauthorized): Sua Chave de API não é válida.</span>"];
            case 404:
                return ['response' => "<span class='text-danger'>Erro 404 (Not Found): A URL ou o modelo não existem. Detalhe: $errorMsg</span>"];
            case 429:
                return ['response' => "<span class='text-warning'>Erro 429 (Too Many Requests): Limite de requisições gratuitas esgotado. Tente mais tarde.</span>"];
            default:
                return ['response' => "<span class='text-danger'>Erro de Rede ($status): $errorMsg</span>"];
        }
    }
}

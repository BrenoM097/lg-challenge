<?php

namespace Tests\Feature;

use App\Productivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSimulationTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function o_controller_retorna_a_analise_da_ia_com_sucesso()
    {
        Productivity::create([
            'product_line' => 'TV',
            'production_unit' => 'Planta A',
            'produced_quantity' => 100,
            'defect_count' => 2,
            'production_date' => '2026-01-10',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Texto gerado simulado pela IA em ambiente de teste.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/dashboard/simulate', [
            'product_line' => 'TV',
            'simulated_quantity' => 1000,
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'response' => 'Texto gerado simulado pela IA em ambiente de teste.'
        ]);
    }
}
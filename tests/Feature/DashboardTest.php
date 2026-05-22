<?php

namespace Tests\Feature;

use App\Productivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function o_dashboard_carrega_com_sucesso()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard de Produtividade');
    }

    /** @test */
    public function o_dashboard_exibe_os_registros_na_tabela_corretamente()
    {
        Productivity::create([
            'production_unit' => 'Planta A',
            'product_line' => 'Geladeira Turbo',
            'produced_quantity' => 85,
            'defect_count' => 2,
            'production_date' => '2026-01-15',
        ]);

        Productivity::create([
            'production_unit' => 'Planta A',
            'product_line' => 'Monitor Super Gamer LG 500hz',
            'produced_quantity' => 62,
            'defect_count' => 0,
            'production_date' => '2026-01-16',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        
        $response->assertSee('Geladeira Turbo');
        $response->assertSee('Monitor Super Gamer LG 500hz');
        
        $response->assertSee('85');
        $response->assertSee('62');
    }
}
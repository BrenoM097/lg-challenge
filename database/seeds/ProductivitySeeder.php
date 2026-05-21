<?php

use Illuminate\Database\Seeder;
use App\Productivity;

class ProductivitySeeder extends Seeder
{
    public function run()
    {
        $production_unit = "Planta A";
        $products = ['Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado'];
        
        for ($day = 1; $day <= 31; $day++) {
            $date = sprintf('2026-01-%02d', $day);
            
            foreach ($products as $product) {
                switch ($product) {
                    case 'TV':
                        $produced = rand(120, 160);
                        $defects = rand(1, 4);
                        if ($day == 15) { $defects = 42; } 
                        break;
                        
                    case 'Geladeira':
                        $produced = rand(70, 90);
                        if ($day > 20) {
                            $defects = rand(8, 14); 
                        } else {
                            $defects = rand(0, 2);
                        }
                        break;
                        
                    case 'Máquina de Lavar':
                        $produced = rand(50, 75);
                        $defects = rand(0, 3);
                        break;
                        
                    case 'Ar-Condicionado':
                        $produced = rand(40, 60);
                        $defects = rand(0, 1);
                        break;
                }

                Productivity::create([
                    'production_unit' => $production_unit,
                    'product_line' => $product,
                    'produced_quantity' => $produced,
                    'defect_count' => $defects,
                    'production_date' => $date,
                ]);
            }
        }
    }
}
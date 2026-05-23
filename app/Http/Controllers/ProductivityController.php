<?php

namespace App\Http\Controllers;

use App\Services\Contracts\ProductivityServiceInterface;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{
    private $productivityService;

    public function __construct(ProductivityServiceInterface $productivityService) 
    {
        $this->productivityService = $productivityService;
    }

    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', '01');
        $viewMode = $request->input('view_mode', 'detailed');
        $sort = $request->input('sort', 'production_date');
        $direction = $request->input('direction', 'desc');

        $availableLines = $this->productivityService->getAvailableLines();
        $productLine = $request->filled('product_line') ? $request->product_line : null;

        $productivities = $this->productivityService->getProductivities(
            intval($year),
            $month,
            $productLine,
            $viewMode,
            $sort,
            $direction
        );

        return view('dashboard', compact('productivities', 'availableLines', 'sort', 'direction', 'year', 'month', 'viewMode'));
    }
}
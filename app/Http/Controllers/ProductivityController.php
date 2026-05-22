<?php

namespace App\Http\Controllers;

use App\Productivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductivityController extends Controller
{
    public function index(Request $request) 
{
    $availableLines = Productivity::distinct()->orderBy('product_line')->pluck('product_line');

    $year = $request->input('year', date('Y'));
    $month = $request->input('month', '01'); 
    $viewMode = $request->input('view_mode', 'detailed');

    $sort = $request->input('sort', 'production_date');
    $direction = strtolower($request->input('direction', 'desc'));

    $query = Productivity::whereYear('production_date', $year);

    if (!empty($month) && intval($month) >= 1 && intval($month) <= 12) {
        $query->whereMonth('production_date', sprintf('%02d', intval($month)));
    } else {
        $month = ''; 
    }

    if ($request->filled('product_line')) {
        $query->where('product_line', $request->product_line);
    }

    if ($viewMode === 'summary') {
        $productivities = $query->selectRaw('product_line, SUM(produced_quantity) as produced_quantity, SUM(defect_count) as defect_count')
                                ->groupBy('product_line')
                                ->orderBy('product_line', 'asc')
                                ->paginate(10);
    } else {
        $sortableColumns = ['production_date', 'product_line', 'produced_quantity', 'defect_count'];

        if (in_array($sort, $sortableColumns) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('production_date', 'desc');
        }

        $productivities = $query->paginate(10);
    }

    return view('dashboard', compact('productivities', 'availableLines', 'sort', 'direction', 'year', 'month', 'viewMode'));
}

}
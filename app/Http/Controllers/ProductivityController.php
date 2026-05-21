<?php

namespace App\Http\Controllers;

use App\Productivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductivityController extends Controller
{
    public function index(Request $request) {
    $availableLines = Productivity::distinct()->orderBy('product_line')->pluck('product_line');

    $query = Productivity::whereBetween('production_date', ['2026-01-01', '2026-01-31']);

    if ($request->filled('product_line')) {
        $query->where('product_line', $request->product_line);
    }

    $productivities = $query->paginate(10);

    return view('dashboard', compact('productivities', 'availableLines'));
}

}
<?php

namespace App\Services;

use App\Productivity;
use App\Services\Contracts\ProductivityServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductivityService implements ProductivityServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAvailableLines()
    {
        return Productivity::distinct()
            ->orderBy('product_line')
            ->pluck('product_line');
    }

    /**
     * {@inheritdoc}
     */
    public function getProductivities(
        int $year,
        ?string $month,
        ?string $productLine,
        string $viewMode,
        string $sort,
        string $direction
    ): LengthAwarePaginator {
        $query = Productivity::whereYear('production_date', $year);

        if (!empty($month) && intval($month) >= 1 && intval($month) <= 12) {
            $query->whereMonth('production_date', sprintf('%02d', intval($month)));
        }

        if (!empty($productLine)) {
            $query->where('product_line', $productLine);
        }

        if ($viewMode === 'summary') {
            return $this->getSummaryView($query);
        }

        return $this->getDetailedView($query, $sort, $direction);
    }

    /**
     * Retorna a visão de resumo (agrupado por linha de produto).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return LengthAwarePaginator
     */
    private function getSummaryView($query): LengthAwarePaginator
    {
        return $query->selectRaw('product_line, SUM(produced_quantity) as produced_quantity, SUM(defect_count) as defect_count')
                    ->groupBy('product_line')
                    ->orderBy('product_line', 'asc')
                    ->paginate(10);
    }

    /**
     * Retorna a visão detalhada (com ordenação).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sort
     * @param string $direction
     * @return LengthAwarePaginator
     */
    private function getDetailedView($query, string $sort, string $direction): LengthAwarePaginator
    {
        $sortableColumns = ['production_date', 'product_line', 'produced_quantity', 'defect_count'];
        $direction = strtolower($direction);

        if (in_array($sort, $sortableColumns) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('production_date', 'desc');
        }

        return $query->paginate(10);
    }
}

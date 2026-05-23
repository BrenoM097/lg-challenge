<?php

namespace App\Services\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProductivityServiceInterface
{
    /**
     * Obtém a lista de linhas de produtos disponíveis.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableLines();

    /**
     * Busca registros de produtividade com filtros e paginação.
     *
     * @param int $year
     * @param string $month
     * @param string|null $productLine
     * @param string $viewMode
     * @param string $sort
     * @param string $direction
     * @return LengthAwarePaginator
     */
    public function getProductivities(
        int $year,
        string $month,
        ?string $productLine,
        string $viewMode,
        string $sort,
        string $direction
    ): LengthAwarePaginator;
}

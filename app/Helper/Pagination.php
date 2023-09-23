<?php

namespace App\Helper;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of Pagination
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
class Pagination extends LengthAwarePaginator
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $this->withQueryString();
        return [
            'data'  => $this->items->toArray(),
            'links' => [
                'first' => $this->url(1),
                'last'  => $this->url($this->lastPage()),
                'prev'  => $this->previousPageUrl(),
                'next'  => $this->nextPageUrl(),
            ],
            'meta'  => [
                'pagination' => [
                    'current_page' => $this->currentPage(),
                    'from'         => $this->firstItem(),
                    'last_page'    => $this->lastPage(),
                    'links'        => $this->linkCollection()->toArray(),
                    'path'         => $this->path(),
                    'per_page'     => $this->perPage(),
                    'to'           => $this->lastItem(),
                    'total'        => $this->total(),
                ],
            ]
        ];
    }
}

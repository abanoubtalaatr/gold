<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GoldPieceFilter
{
    protected $request;
    protected $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $this->query = $query;

        // Base query for approved orders
        // $this->approvedOrders();

        // Apply filters based on request parameters
        return $this
            ->caratFilter()
            ->weightFilter()
            ->priceFilter()
            ->typeFilter()
            ->statusFilter()
            ->searchFilter()
            ->sortByPrice()
            ->getQuery();
    }

    protected function approvedOrders(): self
    {
        $this->query->whereHas('orderRentals', function($query) {
            $query->where('status', 'accepted');
        })->orWhereHas('orderSales', function($query) {
            $query->where('status', 'accepted');
        });

        return $this;
    }

    protected function caratFilter(): self
    {
        if ($this->request->filled(['from_carat', 'to_carat'])) {
            $this->query->whereBetween('carat', [
                $this->request->from_carat,
                $this->request->to_carat
            ]);
        }

        if ($this->request->carat) {
            $this->query->where('carat', $this->request->carat);
        }

        return $this;
    }

    protected function weightFilter(): self
    {
        if ($this->request->filled(['from_weight', 'to_weight'])) {
            $this->query->whereBetween('weight', [
                $this->request->from_weight,
                $this->request->to_weight
            ]);
        }

        if ($this->request->weight) {
            $this->query->where('weight', $this->request->weight);
        }

        return $this;
    }

    protected function priceFilter(): self
    {
        if ($this->request->filled(['from_rental_price', 'to_rental_price'])) {
            $this->query->where('type', 'for_rent')
                ->whereBetween('rental_price_per_day', [
                    $this->request->from_rental_price,
                    $this->request->to_rental_price
                ]);
        }

        if ($this->request->filled(['from_sale_price', 'to_sale_price'])) {
            $this->query->where('type', 'for_sale')
                ->whereBetween('sale_price', [
                    $this->request->from_sale_price,
                    $this->request->to_sale_price
                ]);
        }

        return $this;
    }

    protected function typeFilter(): self
    {
        if ($this->request->type) {
            $this->query->where('type', $this->request->type);
        }

        return $this;
    }

    protected function statusFilter(): self
    {
        if ($this->request->status) {
            $this->query->where('status', $this->request->status);
        }

        return $this;
    }

    protected function searchFilter(): self
    {
        if ($this->request->search) {
            $this->query->where('name', 'like', "%{$this->request->search}%");
        }

        return $this;
    }

    protected function sortByPrice(): self
    {
        if ($this->request->price_sort) {
            $column = $this->request->type === 'for_rent' ? 'rental_price_per_day' : 'sale_price';
            $this->query->orderBy($column, $this->request->price_sort);
        }

        return $this;
    }

    protected function getQuery(): Builder
    {
        return $this->query;
    }
} 
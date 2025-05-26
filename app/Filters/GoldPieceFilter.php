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
        $this->rentalFilter();

        if ($this->request->filled('price_sort')) {
            $this->query->orderBy('rental_price_per_day', $this->request->price_sort);
        }

        return $this
            ->caratFilter()
            ->weightFilter()
            ->priceFilter()
            ->typeFilter()
            ->statusFilter()
            ->searchFilter()
            ->availableToRentFilter()
            ->sortByPrice()
            ->cityFilter()
            ->getQuery();
    }

    protected function availableToRentFilter(): self
    {
        $dateFrom = $this->request->date_from;
        $dateTo = $this->request->date_to;
    
        // Validate dates (optional but recommended)
        if ($dateFrom && $dateTo) {
            $this->query->whereDoesntHave('orderRentals', function ($query) use ($dateFrom, $dateTo) {
                $query->where('start_date', '<=', $dateTo)
                      ->where('end_date', '>=', $dateFrom);
            });
        }
    
        return $this;
    }

    protected function ratingsFilter(): self
    {
        if ($this->request->filled('rating')) {
            $rating = $this->request->rating;
            
            $this->query->whereHas('ratings', function($query) use ($rating) {
                $query->where('rating', $rating);
            });
        }

        return $this;
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
    protected function rentalFilter(): self
    {
        $this->query->whereHas('orderRentals');

        return $this;
    }
    protected function caratFilter(): self
    {
        if ($this->request->filled('carat')) {
            $this->query->where('carat', $this->request->carat);
        }

        return $this;
    }

    protected function cityFilter(): self
    {
        if ($this->request->filled('city_id')) {
            $this->query->whereHas('orderRentals', function($query) {
                $query->whereHas('branch', function($query) {
                    $query->where('city_id', $this->request->city_id);
                });
            });
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
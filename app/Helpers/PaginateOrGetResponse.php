<?php

namespace App\Helpers;

class PaginateOrGetResponse
{
    public function data($query, $request): object
    {

        $tableName = $query->getModel()->getTable();

        $order = $request->orderBy?? $request->order_by?? $tableName.'.created_at';
        $order_type = $request->orderType ?? $request->order_type?? 'DESC';
        $per_page = $request->per_page ?? config('app.pagination_limit');

        $query->when($request->limit, fn($qu, $limit) => $qu->limit($limit));
        
       ($request->is_random)? $query->inRandomOrder() : $query->orderBy($order, $order_type);


        $items = ($request->is_paginated)? $query->paginate($per_page) : $query->get()->unique('id');

        return $items;
    }
}

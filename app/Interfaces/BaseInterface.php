<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
    public function create(array $data);
    public function updateById($id, array $data, array $options = []);
    public function update($model, array $data, array $options = []);
    public function delete();
    public function deleteModel(Model $model);
    public function limit($limit);
    public function orderBy($column, $direction = 'asc');
    public function where($column, $value, $operator = '=');
    public function whereIn($column, $values);
    public function whereNotIn($column, $values);
    public function with($relations);
    public function deleteById($id): bool;
    public function deleteMultipleById(array $ids);
    public function first(array $columns = ['*']);
    public function get(array $columns = ['*']);
    public function getByColumn($item, $column, array $columns = ['*']);
    public function paginate($limit = 25, $page = null , $pageName = 'page', array $columns = ['*']);
    public function getById($id, array $columns = ['*']);
    public function count(): int;
    public function createMultiple(array $data);
}

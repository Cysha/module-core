<?php

namespace Cms\Modules\Core\Repositories;

interface BaseRepositoryInterface
{
    public function getModel();

    public function all();

    public function count();

    public function create(array $data);

    public function createMultiple(array $data);

    public function delete();

    public function deleteById($id);

    public function deleteMultipleById(array $ids);

    public function first();

    public function findOrCreate(array $where, array $attributes = []);

    public function get();

    public function getById($id);

    public function limit($limit);

    public function orderBy($column, $value);

    public function updateById($id, array $data);

    public function where($column, $value, $operator = '=');

    public function whereIn($column, $value);

    public function with($relations);

    public function has($relation);

    public function paginate($perPage = 0, $columns = ['*']);

    public function transformModels($data);
}

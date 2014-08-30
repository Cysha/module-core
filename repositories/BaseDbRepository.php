<?php namespace Cysha\Modules\Core\Repositories;

abstract class BaseDbRepository
{

    /**
     * Get all the records
     *
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     */
    public function make(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
     * Find an entity by id
     *
     * @param  int $primary_key
     * @param  array $with
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getById($primary_key, array $with = array())
    {
        return $this->make($with)->find($primary_key);
    }

    /**
     * Get all results from a model
     *
     * @param  array $with
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $with = array())
    {
        return $this->make($with)->all();
    }

    /**
     * Get all results from a model
     *
     * @param  string $name
     * @param  array $with
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getBySlug($name, $with = array())
    {
        $one = $this->make($with)->all()->filter(function ($m) use ($name) {
            return $name == $m->makeSlug();
        });

        return $this->transformModel($one->first());
    }

    /**
     * Find a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     */
    public function getFirstBy($key, $value, array $with = array(), $raw = false)
    {
        $data = $this->manyBy($key, $value, $with, $raw)->first();

        return ($raw === true ? $data : $this->transformModel($data));
    }

    /**
     * Find many entities by key value
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     */
    public function getManyBy($key, $value, array $with = array(), $raw = false)
    {
        $data = $this->manyBy($key, $value, $with, $raw)->get();

        return ($raw === true ? $data : $this->transformModel($data));
    }

    public function manyBy($key, $value, $with = array())
    {
        return $this->make($with)->where($key, '=', $value);
    }

    public function transformModel($data)
    {
        $transformed = [];
        if (count($data) > 1) {
            $data->each(function ($row) use (&$transformed) {
                $transformed[] = $row->transform();
            });
        } else {
            $transformed[] = $data->transform();
        }

        if (!empty($transformed)) {
            $data = $transformed;
        }

        return $data;
    }

    public function paginate($perPage = 0, $columns = array('*'))
    {
        $perPage = $perPage ?: \Config::get('forum::paginate.perPage', 10);

        return $this->paginate($perPage, $columns);
    }
}

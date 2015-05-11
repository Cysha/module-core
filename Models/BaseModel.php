<?php namespace Cms\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    //use \Cms\Modules\Core\Traits\LinkableTrait;

    //protected $identifiableName = 'name';

    //public function identifiableName()
    //{
    //    return $this->{$this->identifiableName};
    //}

    /**
     * Fill attributes in $this from Input
     *
     * @return Model
     */
    public function hydrateFromInput(array $input = [])
    {
        if (!isset($this->fillable)) {
            return $this->fill(\Input::all());
        }

        if (empty($input)) {
            $input = \Input::only($this->fillable);
        } else {
            $input = array_only($input, $this->fillable);
        }

        $input = array_filter($input, 'strlen');

        return $this->fill($input);
    }

    public function transform()
    {
        return $this->toArray();
    }

}

<?php namespace Cysha\Modules\Core\Models;

class BaseModel extends \Eloquent
{
    use \Cysha\Modules\Core\Traits\LinkableTrait;

    protected $identifiableName = 'name';

    public function __constructor()
    {
        \Cysha\Modules\Core\Traits\LinkableTrait::constructor();
    }

    public function identifiableName()
    {
        return $this->{$this->identifiableName};
    }

    public function scopeFindOrCreate($query, array $where, array $attrs = array())
    {

        $objModel = static::firstOrCreate($where);

        if (count($attrs) > 0) {
            $objModel->fill($attrs);

            if (count($objModel->getDirty())) {
                $objModel->save();
            }
        }

        return $objModel;
    }

    /**
     * Fill attributes in $this from Input
     */
    public function hydrateFromInput(array $input = array())
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

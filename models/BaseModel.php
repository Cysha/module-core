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

}

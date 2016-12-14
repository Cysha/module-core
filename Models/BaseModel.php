<?php

namespace Cms\Modules\Core\Models;

use Cms\Modules\Core\Traits\LinkableTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use LinkableTrait;

    protected $identifiableName = 'name';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Fire up the linkableTrait so it can do its thing.
     */
    public function __construct()
    {
        parent::__construct();

        self::linkableConstructor();
    }

    /**
     * Gives the model a identifiable name for links and such.
     *
     * @return string
     */
    public function identifiableName()
    {
        return $this->{$this->identifiableName};
    }

    /**
     * Fill attributes in $this from Input.
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

    /**
     * Beatswitch\Lock Methods.
     */
    public function getCallerType()
    {
        list(, , $module, , $model) = explode('\\', __CLASS__);

        return sprintf('%s_%s', $module, $model);
    }

    public function getCallerId()
    {
        return $this->id;
    }

    public function getCallerRoles()
    {
        return [];
    }

    public function transform()
    {
        return $this->toArray();
    }
}

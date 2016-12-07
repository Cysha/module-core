<?php

namespace Cms\Modules\Core\Models;

class Navigation extends BaseModel
{
    public $table = 'core_navigation';

    protected $fillable = ['name', 'class'];

    public function links()
    {
        return $this->hasMany(__NAMESPACE__.'\NavigationLink');
    }
}

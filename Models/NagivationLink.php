<?php

namespace Cms\Modules\Core\Models;

class NavigationLink extends BaseModel
{
    public $table = 'core_navigation_links';

    protected $fillable = ['name', 'class'];

    public function navigation()
    {
        return $this->belongsTo(__NAMESPACE__.'\Navigation');
    }
}

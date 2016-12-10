<?php

namespace Cms\Modules\Core\Models;

class NavigationLink extends BaseModel
{
    public $table = 'core_navigation_links';

    protected $fillable = ['navigation_id', 'title', 'url', 'route', 'class', 'blank', 'order'];

    public function navigation()
    {
        return $this->belongsTo(__NAMESPACE__.'\Navigation');
    }
}

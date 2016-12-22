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

    public function linkCount()
    {
        return $this->hasOne(__NAMESPACE__.'\NavigationLink')
            ->selectRaw('navigation_id, count(*) as aggregate')
            ->groupBy('navigation_id');
    }

    public function getLinkCountAttribute()
    {
        if (!array_key_exists('linkCount', $this->relations)) {
            $this->load('linkCount');
        }

        $related = $this->getRelation('linkCount');

        return $related ? $related->aggregate : 0;
    }
}

<?php

namespace Cms\Modules\Core\Traits;

trait LinkableTrait
{
    public function linkableConstructor()
    {
        if (!$this->link) {
            return;
        }

        if ($this->getOriginal('slug')) {
            return;
        }

        $appends = ['slug'];
        $this->appends = is_array($this->appends)
            ? array_merge($this->appends, $appends)
            : $appends;
    }

    public function getSlugAttribute()
    {
        if ($this->getOriginal('slug')) {
            return $this->getOriginal('slug');
        }

        return str_slug($this->{$this->identifiableName}, '-');
    }

    public function makeLink($urlOnly = false)
    {
        if (empty($this->attributes)) {
            return;
        }

        $link = (object) $this->link;
        if (!isset($link->route)) {
            throw new \InvalidArgumentException('Could not find a route. '.get_class($this));

            return;
        }
        if (!isset($link->attributes)) {
            throw new \InvalidArgumentException('Could not find any attributes. '.get_class($this));

            return;
        }
        if (!isset($this->identifiableName)) {
            throw new \InvalidArgumentException('Could not find a identifiableName. '.get_class($this));

            return;
        }

        $attributes = [];
        foreach ($link->attributes as $key => $attr) {
            if (!isset($this->{$attr})) {
                throw new \InvalidArgumentException('Cant use "'.$attr.'" attribute from model "'.get_class($this).'"');
            }
            $attributes[$key] = $this->{$attr};
        }

        $url = route($link->route, $attributes);
        if ($urlOnly) {
            return $url;
        }

        return \HTML::link($url, $this->{$this->identifiableName});
    }
}

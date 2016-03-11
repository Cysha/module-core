<?php

namespace Cms\Modules\Core\Traits;

trait LinkableTrait
{
    public function linkableConstructor()
    {
        $appends = ['slug'];
        $this->appends = is_array($this->appends)
            ? array_merge($this->appends, $appends)
            : $appends;
    }

    public function getSlugAttribute()
    {
        return str_slug($this->{$this->identifiableName}, '-');
    }

    public function makeLink($urlOnly = false)
    {
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

<?php namespace Cysha\Modules\Core\Models;

class BaseModel extends \Eloquent
{

    protected $identifiableName = 'name';

    public function __construct()
    {
        parent::__construct();

        $appends = ['slug'];
        $this->appends = is_array($this->appends) ? array_merge($this->appends, $appends) : $appends;
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

    public function makeSlug()
    {
        return \Str::slug($this->{$this->identifiableName}, '-');
    }

    public function makeLink($urlOnly = false)
    {
        $link = (object) $this->link;
        if (!isset($link->route) || !isset($link->attributes) || !isset($this->identifiableName)) {
            \DebugBar::addMessage('Could not generate link for model'.get_class($this));

            return null;
        }

        foreach ($link->attributes as &$attr) {
            if (!isset($this->{$attr})) {
                throw new \InvalidArgumentException('Cant use "'.$attr.'" attribute from model "'.get_class($this).'"');
            }
            $attr = $this->{$attr};
        }

        $url = \URL::route($link->route, $link->attributes);
        if ($urlOnly) {
            return $url;
        }

        return \HTML::link($url, $this->{$this->identifiableName});
    }

    public function getSlugAttribute()
    {
        return $this->makeSlug();
    }

}

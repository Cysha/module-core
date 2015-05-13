<?php namespace Cms\Modules\Core\Models;

use Cache;
use DB;
use Event;

class DBConfig extends BaseModel
{
    public $table = 'config';
    public $timestamps = false;

    protected $fillable = ['environment', 'group', 'namespace', 'item', 'value'];
    public $appends = ['key'];

    /**
     * Sets a settings value
     *
     * @return bool
     */
    public function set($setting, $value)
    {
        $this->fill($this->explodeSetting($setting, $value));
        return $this->save();
    }

    /**
     * Explodes the setting passed into its separate parts
     *
     * @return array
     */
    public function explodeSetting($setting, $value = null)
    {
        $item = $setting;
        $namespace = null;
        if (strpos($setting, '::') !== false) {
            list($namespace, $item) = explode('::', $setting);
        }

        $group = null;
        if (strpos($item, '.') !== false) {
            $group = str_replace(substr(strrchr($item, '.'), 0), '', $item);
            $item = substr(strrchr($item, '.'), 1);
        }
        $environment = app()->environment();

        return array_filter(compact('environment', 'group', 'namespace', 'item', 'value'));
    }


    /**
     *
     */
    public function getNamespaceAttribute($value)
    {
        return str_replace('.', '_', $value);
    }


    /**
     *
     */
    public function getKeyAttribute()
    {
        // see if we can gather the settings info
        $key = implode('.', [$this->group, $this->item]);
        if (!empty($this->namespace)) {
            $key = sprintf('%s::%s', $this->namespace, $key);
        }

        // fix an issue with no group on the setting
        return str_replace('::.', '::', $key);
    }

    public function getValueAttribute($value)
    {
        $value = json_decode($value);
        return $value;
    }

    public function setValueAttribute($value)
    {
        if (strlen($value) == 0 || $value === null) {
            $this->attributes['value'] = NULL;
            return;
        }

        $this->attributes['value'] = json_encode($value);
    }

}

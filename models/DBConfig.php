<?php namespace Cysha\Modules\Core\Models;

use App;
use Cache;
use DB;

class DBConfig extends \Eloquent
{
    public $table = 'config';
    public $timestamps = false;

    protected $fillable = array('environment', 'group', 'namespace', 'item', 'value');

    /** ModelEvents **/
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget('core.config_table');
            Event::fire('core.config.save', ['key' => $model->key, 'value' => $model->value]);
        });
    }

    /**
     *
     *
     *
     **/
    public function set($setting, $value)
    {
        $this->fill($this->explodeSetting($setting, $value));
        return $this->save();
    }

    /**
     * Explodes the setting passed into its separate parts
     *
     * @return array
     **/
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
        $environment = App::Environment();

        return array_filter(compact('environment', 'group', 'namespace', 'item', 'value'));
    }


    /**
     *
     **/
    public function getKeyAttribute()
    {
        // see if we can gather the settings info
        $key = implode('.', [$this->group, $this->item]);
        if ($this->namespace !== null) {
            $key = $this->namespace.'::'.$key;
        }

        //fix an issue with no group on the setting
        return str_replace('::.', '::', $key);
    }

    public function getValueAttribute($value)
    {
        return json_decode($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode($value);
    }

}

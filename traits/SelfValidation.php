<?php namespace Cysha\Modules\Core\Traits;

use Illuminate\Support\MessageBag;
use Validator;
use Input;

trait SelfValidation
{
    /**
     * Validation error message bag
     *
     * @var Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Input Array
     *
     */
    protected $input = array();

    /**
     * Validator instance
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Listen for save event
     */
    public static function boot()
    {
        parent::boot();

        if (isset(static::$rules['creating']) && isset(static::$rules['updating'])) {
            static::creating(function ($model) {
                return self::doCheck($model, 'creating');
            });
            static::updating(function ($model) {
                return self::doCheck($model, 'updating');
            });
        } else {
            static::saving(function ($model) {
                return self::doCheck($model, 'saving');
            });
        }
    }

    protected static function doCheck($model, $ruleset)
    {
        // ensure we have a ruleset to work with
        $_ruleset = ( isset(self::$rules[$ruleset]) ? self::$rules[$ruleset] : array() );
        if (!count($_ruleset)) {
            $_ruleset = ( isset(self::$rules['creating']) ? self::$rules['creating'] : array() );
        }

        // try validate the model
        if (!$model->validate($_ruleset)) {
            return false;
        }

        // if we dont have anything to purge, return true
        if (empty($model::$purge)) {
            return true;
        }

        // else purge the keys we dont need, and return true
        foreach ($model::$purge as $key) {
            unset($model->attributes[$key]);
        }

        return true;
    }

    /**
     * Validates current attributes against rules
     *
     * @param array $rules    Optional validation rules to override static::$validationRules.
     * @param array $messages Optional validation messages to override static::$validationMessages.
     *
     * @return boolean
     */
    public function validate(array $rules = array(), array $messages = array())
    {
        $rules = $this->processRules($rules ?: static::$rules);
        $messages = $messages ?: static::$messages;

        $validateAgainst = ( count($this->input) ? $this->input : $this->attributes );

        $v = Validator::make($validateAgainst, $rules, $messages);
        if ($v->passes() === false) {
            $this->setErrors($v->messages());
        }

        return ( $v->passes() ? true : false );
    }

    /**
     * Process validation rules, and replace any variables in there.
     *
     * @param  array $rules
     * @return array $rules
     */
    protected function processRules(array $rules)
    {
        array_walk($rules, function (&$item) {
            if (!is_string($item)) {
                 return;
            }
            $match = preg_match('/\:([A-Za-z_-]*):/se', $item, $m);
            if ($match === false) {
                return;
            }
            if (!count($m)) {
                 return;
            }

            if (!isset($this->{$m[1]})) {
                 return;
            }
            $item = str_replace(':'.$m[1].':', $this->{$m[1]}, $item);
        });

        return $rules;
    }

    /**
     * Set error message bag
     *
     * @var Illuminate\Support\MessageBag
     *
     * @return null
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Retrieve error message bag
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Determine if there are any validation errors.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->errors->messages);
    }

    /**
     * Fill attributes in $this from \Input
     *
     * @see \Illuminate
     */
    public function hydrateFromInput(array $input = array())
    {
        if (empty($input)) {

            $input = Input::only($this->fillable);
        } else {
            $input = array_only(Input::all(), $this->fillable);
        }

        $this->input = array_filter($input, 'strlen');

        return $this->fill($this->input);
    }
}

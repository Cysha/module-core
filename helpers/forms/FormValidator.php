<?php namespace Cysha\Modules\Core\Helpers\Forms;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

abstract class FormValidator
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var ValidatorInstance
     */
    protected $validation;

    /**
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;

        if (method_exists($this, 'register')) {
            $this->register();
        }
    }

    /**
     * Validate the form data
     *
     * @param array $formData
     * @return mixed
     * @throws FormValidationException
     */
    public function validate(array $formData)
    {
        $this->validation = $this->validator->make($formData, $this->getValidationRules(), $this->getValidationMessages());

        if ($this->validation->fails()) {
            throw new FormValidationException('Validation failed', $this->getValidationErrors());
        }

        return true;
    }

    /**
     * Get the validation rules
     *
     * @return array
     */
    protected function getValidationRules()
    {
        return $this->processRules($this->rules);
    }

    /**
     * Get the validation messages
     *
     * @return array
     */
    protected function getValidationMessages()
    {
        return isset($this->messages) ? $this->messages : [];
    }

    /**
     * Get the validation errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    protected function getValidationErrors()
    {
        return $this->validation->errors();
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

}

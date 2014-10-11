<?php namespace Cysha\Modules\Core\Helpers\Forms;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Validation\Validator as ValidatorInstance;
use Lang;

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
        $this->authorizeUser();

        $this->validation = $this->validator->make($formData, $this->getValidationRules(), $this->getValidationMessages());

        if ($this->validation->fails()) {
            throw new FormValidationException(Lang::get('core::forms.validation.title'), $this->getValidationErrors());
        }

        return true;
    }

    /**
     * Check if the user is allowed to submit this form.
     *
     * @return mixed
     * @throws FormUnauthorizedException
     */
    public function authorizeUser()
    {
        if (!method_exists($this, 'authorize')) {
            return true;
        }

        if ($this->authorize() !== true) {
            throw new FormUnauthorizedException(Lang::get('core::forms.unauthorized.title'), $this->getValidationErrors());
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
        return $this->processRules($this->_rules());
    }

    /**
     * Get the validation messages
     *
     * @return array
     */
    protected function getValidationMessages()
    {
        $messages = $this->_messages();

        return isset($messages) ? $messages : [];
    }

    /**
     * Get the validation errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    protected function getValidationErrors()
    {
        if (method_exists($this->validation, 'errors')) {
            return $this->validation->errors();
        }

        return with(new \Illuminate\Support\MessageBag);
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

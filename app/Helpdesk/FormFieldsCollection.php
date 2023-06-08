<?php

namespace App\Helpdesk;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormFieldsCollection
{
    /**
     * @var FormFiled[] $items
     */
    public array $items = [];

    public array $rules = [];

    public array $messages = [];

    public array $formData = [];

    protected Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $fields = $request->get('formData');
        $files = $request->files->get('formData');
        if (!is_null($fields)) {
            foreach ($fields as $key => $field) {
                if (!isset($field['value'])) {
                    $field['value'] = $files[$key]['value'];
                }
                $field = new FormFiled($field);
                if (isset($field->options['rules'])) {
                    $this->rules[$field->name] = $this->prepareRule($field->options['rules']);
                }
                $this->items[] = $field;
                //TODO CUSTOMIZE ERROR MESSAGES BY TYPES (required, mime, min max)
                $this->messages[$field->name] = __('validation.error_saving_field', ['field' => $field->field->name]);
                $this->formData[$field->name] = $field->value;
            }
        }

        return $this;
    }

    protected function prepareRule(array|\stdClass $_rule)
    {
        $ruleArr = (array)$_rule;
        $rule = [];
        foreach ($ruleArr as $key => $item) {
            $rule[] = !is_numeric($key) ? $key . ':' . $item : $item;
        }
        return implode('|', $rule);
    }

    /**
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        $validator = Validator::make($this->formData, $this->rules, $this->messages);
        $validator->validate();
        return !$validator->fails();
    }
}

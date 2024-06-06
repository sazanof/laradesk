<?php

namespace App\Helpdesk;

use App\Models\FieldCategory;
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

    public array $attributes = [];

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

        $dbFields = FieldCategory
            ::with('field')
            ->where('category_id', $this->request->get('category_id'))
            ->get();
        if ($dbFields->isNotEmpty()) {
            $i = 0;
            foreach ($dbFields as $dbField) {
                $_field = new FormFiled($dbField);
                if (!is_null($fields)) {
                    $fieldIdx = 0;
                    foreach ($fields as $key => $field) {
                        if (intval($field['category_field_id']) === $dbField->id) {
                            if (array_key_exists($fieldIdx, $files)) {
                                $field['value'] = $files[$fieldIdx]['value'];
                            }
                            $_field->passRequestValue($field);
                        }
                        $fieldIdx++;
                    }
                }
                if (isset($_field->options['rules'])) {
                    $this->rules[$_field->name] = $this->prepareRule($_field->options['rules']);
                    $this->prepareMessages($_field);
                }
                $this->items[] = $_field;
                //TODO CUSTOMIZE ERROR MESSAGES BY TYPES (required, mime, min max)
                //$this->messages[$_field->name] = __('validation.error_saving_field', ['field' => $_field->field->name]);
                $this->formData[$_field->name] = $_field->value;
                $i++;
            }
        }
        /* if (!is_null($fields)) {
             foreach ($fields as $key => $field) {
                 if (!empty($field['value'])) {
                     $field['value'] = $files[$key]['value'];
                 }
                 $field = new FormFiled($field);
                 dd($field);
                 if (isset($field->options['rules'])) {
                     $this->rules[$field->name] = $this->prepareRule($field->options['rules']);
                 }
                 $this->items[] = $field;
                 //TODO CUSTOMIZE ERROR MESSAGES BY TYPES (required, mime, min max)
                 $this->messages[$field->name] = __('validation.error_saving_field', ['field' => $field->field->name]);
                 $this->formData[$field->name] = $field->value;
             }
         } else {

             // todo если пользователь отправляет заявку сразу, то массив полей пуст. надо достать поля из базы и сверится с тем, что приходит
         }*/
        //dd($this->rules, $this->messages);

        return $this;
    }

    protected function prepareRule(array|\stdClass $_rule)
    {
        $ruleArr = (array)$_rule;
        $rule = [];
        foreach ($ruleArr as $key => $item) {
            $realKey = !is_numeric($key) ? $key . ':' . $item : $item;
            $rule[] = $realKey;
        }
        return implode('|', $rule);
    }

    protected function prepareMessages(FormFiled $field)
    {
        $rules = (array)$field->options['rules'];
        if (!empty($field->options) && array_key_exists('rules', $field->options)) {
            foreach ((array)$field->options['rules'] as $key => $rule) {
                $realKey = !is_numeric($key) ? $key : $rule;
                $hasValue = !is_numeric($key);
                $messageKey = $field->name . '.' . $realKey;
                $t = match ($realKey) {
                    'required' => "required",
                    'min' => "min",
                    default => "def",
                };
                $this->attributes[$field->name] = $field->field->name;
                $this->messages[$messageKey] = $t;
            }
        }
    }

    /**
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        $validator = Validator::make($this->formData, $this->rules, [], $this->attributes);
        $validator->validate();
        return !$validator->fails();
    }
}

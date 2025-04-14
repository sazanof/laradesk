<?php

namespace App\Helpdesk;

use App\Helpers\FieldHelper;
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
            /** @var FieldCategory $dbField */
            foreach ($dbFields as $dbField) {
                $_field = new FormFiled($dbField);
                if (!is_null($fields)) {
                    $fieldIdx = 0;
                    foreach ($fields as $key => $field) {
                        if (intval($field['category_field_id']) === $dbField->id) {
                            if (is_array($files) && array_key_exists($fieldIdx, $files)) {
                                $field['value'] = $files[$fieldIdx]['value'];
                            }
                            $_field->passRequestValue($field);

                            if ($dbField->field->type === FieldHelper::TYPE_MULTI_JSON) {
                                if ($dbField->required) {
                                    $this->validateMultiJson($dbField, $field['value']);
                                }
                            }

                        }
                        $fieldIdx++;
                    }
                }
                if (isset($_field->options['rules'])) {
                    $this->rules[$_field->name] = $this->prepareRule($_field->options['rules']);
                    $this->prepareMessages($_field);
                }
                //dd($this->rules, $this->attributes);

                $this->items[] = $_field;
                //TODO CUSTOMIZE ERROR MESSAGES BY TYPES (required, mime, min max)
                //$this->messages[$_field->name] = __('validation.error_saving_field', ['field' => $_field->field->name]);
                $this->formData[$_field->name] = $_field->value;
                $i++;
            }
        }
        return $this;
    }

    public function validateMultiJson(FieldCategory $fieldCategory, array $values)
    {
        $required = (bool)$fieldCategory->required;
        $fieldSettings = $fieldCategory->field->options['fields'];
        // Проверка на пустое значение в многомерном массиве JSON и добавление сообщений
        if ($required) {
            foreach ($values as $item) {
                foreach ($fieldSettings as $key => $fieldSetting) {
                    /**
                     * array:2
                     * "index" => "..."
                     * "value" => "..."
                     * ]
                     */
                    if ($item[$key]['value'] === null || $item[$key]['value'] === '') {
                        $arrKey = $fieldCategory->id . '.json.' . $key;
                        $this->rules[$arrKey] = 'required';
                        $this->attributes[$arrKey] = $fieldSetting['title'];
                        $this->messages[$arrKey] = 'required';
                    }
                }
            }
        }

        //dd($this->rules, $this->attributes);
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
        if (!empty($field->options) && array_key_exists('rules', $field->options)) {
            foreach ((array)$field->options['rules'] as $key => $rule) {
                $realKey = !is_numeric($key) ? $key : $rule;
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

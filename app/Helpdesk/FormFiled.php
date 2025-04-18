<?php

namespace App\Helpdesk;

use App\Models\Field;
use App\Models\FieldCategory;

class FormFiled
{
    public int $id;
    public string $name;
    public Field $field;
    public mixed $value;
    protected bool $required;
    public array|\stdClass|null $options = null;

    public function __construct(array|FieldCategory $field)
    {
        if (is_array($field)) {
            $categoryField = FieldCategory::findOrFail($field['category_field_id']);
            $this->field = $categoryField->field;
            $this->id = $this->field->id;
            $this->name = $field['name'];
            $this->required = $categoryField->required;
            $this->value = $field['value'] ?? null;
            $this->options = json_decode($this->field->options, true) ?? [];
            if ($this->required) {
                $merged = isset($this->options['rules']) ? array_merge(['required'], $this->options['rules']) : ['required'];
                $this->options['rules'] = $merged;
            }
        } elseif ($field instanceof FieldCategory) {
            $categoryField = $field;
            $this->field = $categoryField->field;
            $this->id = $this->field->id;
            $this->name = 'field_' . $this->field->id;
            $this->required = $categoryField->required;
            $this->value = null;
            $this->options = json_decode($this->field->options, true) ?? [];
            $merged = isset($this->options['rules'])
                ? array_merge([$this->required ? 'required' : 'nullable'], $this->options['rules'])
                : [$this->required ? 'required' : 'nullable'];
            $this->options['rules'] = $merged;

        }
    }

    public function passRequestValue(array $field)
    {
        if (!empty($field['value'])) {
            $this->value = is_array($field['value'])
                ? json_encode($field['value'], JSON_UNESCAPED_UNICODE)
                : $field['value'];
        }
    }

    public static function make(array $field)
    {
        return new static($field);
    }
}

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

    public function __construct(array $field)
    {
        $categoryField = FieldCategory::findOrFail($field['category_field_id']);
        $this->field = $categoryField->field;
        $this->id = $this->field->id;
        $this->name = $field['name'];
        $this->required = $categoryField->required;
        $this->value = $field['value'] ?? null;
        $this->options = json_decode($this->field->options, true) ?? [];
        if ($this->required) {
            $this->options = array_merge($this->options, [
                'rules' => ['required']
            ]);
        }
    }

    public static function make(array $field)
    {
        return new static($field);
    }
}

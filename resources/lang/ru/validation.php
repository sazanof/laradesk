<?php
return [
    'error_saving_field' => 'Ошибка сохранения поля ":field"',
    '404' => 'Ресурс не найден',
    'keys' => [
        'subject' => 'Тема',
    ],
    'attributes' => [
        'subject' => 'Тема',
        'content' => 'Детали обращения',
        'room_id' => 'Кабинет',
        'custom_location' => 'Произвольное местоположение'
    ],
    'required' => 'Поле ":attribute" обязательно к заполнению',
    'required_without' => 'Поле ":attribute" обязательно если не заполнено(ны) ":values"',
    'mimes' => 'Файл ":attribute" должен быть в формате ":values"',
    'min' => [
        'array' => 'Поле ":attribute" должно иметь минимум :min значение(я).',
        'file' => 'Файл ":attribute" должен быть не меньше :min kb.',
        'numeric' => 'Поле ":attribute" должно быть не менее :min.',
        'string' => 'Длина поля ":attribute" должна быть не менее :min символов.',
    ],
];

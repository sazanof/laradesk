<?php
return [
    'ticket' => [
        'author' => 'Автор',
        'approvals' => 'Согласующие',
        'observers' => 'Наблюдатели',
        'created' => 'Дата создания',
        'category' => 'Категория',
        'comment' => [
            'author' => 'Автор',
            'content' => 'Содержание',
            'created' => 'Дата создания',
            'new' => 'Новый комментарий к заявке #:id',
            'approve' => 'Согласование в заявке #:id',
            'decline' => 'Отказ согласования в заявке #:id',
            'solution' => 'Заявка #:id решена',
            'close' => 'Заявка #:id закрыта',
            'reopen' => 'Заявка #:id переоткрыта',
            'common_text' => ':department: :fullname прокомментировал(а) заявку ":subject"',
        ],
        'role' => [
            'text' => 'Роль',
            'approval' => 'Согласующий',
            'observer' => 'Наблюдатель',
            'assignee' => 'Исполнитель',
            'requester' => 'Заявитель',
        ],
        'new' => [
            'title' => 'Новая заявка :subject #(:id)',
            'text' => ':department: :fullname создал(а) заявку ":subject" в категории ":category"'
        ],
        'office' => 'Офис',
        'participant' => [
            'you_added_to_ticket' => 'Вас добавили в заявку #:id',
            'assignee' => 'Добавление исполнителя к заявке #:id',
            'observer' => 'Добавление наблюдателя к заявке #:id',
            'approval' => 'Добавление согласующего к заявке #:id',
            'new' => [
                'title' => ':fullname добавлен(а) как :role в заявку ":subject"',
                'text' => ':department: :fullname1 добавил(а) пользователя :fullname2 в заявку ":subject" (#:id).'
            ]
        ],
        'room' => 'Кабинет',
        'title' => 'Название заявки',
        'url' => 'Перейти к заявке',
    ],
    'request' => [
        'title' => 'Запрос изменения данных пользователя :username',
        'text' => 'Пользователь :firstname :lastname запросил изменение данных.',
        'message_text' => 'Текст сообщения:'
    ],
    'export' => [
        'finished' => [
            'title' => 'Экспорт завершен успешно',
            'text' => 'Задание по экспорту завершено успешно. По ссылке выможете скачать файл.'
        ]
    ],
    'links' => [
        'go' => 'Перейти'
    ]
];

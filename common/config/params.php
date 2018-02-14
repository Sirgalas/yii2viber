<?php
return [
    'adminEmail' => 'admin@vibershop24.ru',
    'supportEmail' => 'admin@vibershop24.ru',
    'user.passwordResetTokenExpire' => 3600,
    'cookieDomain' => '.vibershop24.ru',
    'frontendHostInfo' => 'http://vibershop24.ru',
    'backendHostInfo' => 'http://vibershop24.ru/admin',
    'coast'=>1,
    'viber' => [

        'login' => 'viber1804',
        'secret' => 'NxQTivMZ',
        'from' => 'Clickbonus',
        'transaction_size_limit'=>999, // максимальное количество телефонов в одной транзакции
        'min_delay' => 2, // Минимальное время между отправкой транзакций
    ],
];

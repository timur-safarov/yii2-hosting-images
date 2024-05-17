<?php
return [

    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'bsVersion' => '5.x',
    'bsDependencyEnabled' => true,
    'pageSize' => 5, //Количество записей настраницу
    'maxSize' => 1024 * 1024 * 100, //Максимальный размер для загрузки файлов
    'maxFileCount' => 5,
    'allowedFileExtensions' => [
        'jpg', 'png', 'gif', 'jpeg', 'ico', 'bmp',
    ],
    'filePath' => '/images/',
    'tempFolder' => '/temp/',
    'icon-framework' => 'fa', 

];

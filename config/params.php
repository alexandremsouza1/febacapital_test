<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'jwt' => [
        'issuer' => 'your-issuer',  // Identificador do emissor
        'audience' => 'your-audience', // Identificador do público
        'id' => '4f1g23a12aa',     // ID único do token
        'request_time' => '+0 seconds',  // Tempo antes de poder ser usado
        'expire' => '+1 hour',      // Tempo de expiração do token
    ],
    'yiisoft/yii-swagger' => [
        'annotation-paths' => [
          '@app/controllers',
        ],
    ],
];

<?php
return [
    'service_manager' => [
        'invokables' => [
            'UserRepository' => 'KmbMemoryInfrastructure\Service\UserRepository',
            'EnvironmentRepository' => 'KmbMemoryInfrastructure\Service\EnvironmentRepository',
        ],
    ],
];

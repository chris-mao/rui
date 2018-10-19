<?php
/**
 * Created by PhpStorm.
 * User: cmao
 * Date: 2018/10/17
 * Time: 15:47
 */

namespace Authentication;

use Authentication\Factory\AuthenticationServiceFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Authentication\Factory\IndexControllerFactory;

return [
    'router' => [
        'routes' => [
            'authentication' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/auth',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'signin',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'signin' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/signin',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'signin'
                            ],
                        ],
                    ],
                    'signout' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/signout',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'signout'
                            ],
                        ],
                    ],
                ],
            ]
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => IndexControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'invokables' => [],
        'aliases' => [],
        'factories' => [
            'AuthenticationService' => AuthenticationServiceFactory::class,
            'PermissionService'     => 'Authentication\Factory\PermissionServiceFactory',
            'RoleService'           => 'Authentication\Factory\RoleServiceFactory',
            'CredentialService'     => 'Authentication\Factory\CredentialServiceFactory',
        ],
    ],

    'view_manager' => [
        'forbidden_template' => 'error/403',
        'template_map' => [
            'layout/signin' => __DIR__ . '/../view/layout/signin.phtml',
            'error/403'   => __DIR__ . '/../view/error/403.phtml',
        ],
        'template_path_stack' => [
            'index' => __DIR__ . '/../view',
        ],
    ],
];

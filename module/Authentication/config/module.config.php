<?php
/**
 * Created by PhpStorm.
 * User: cmao
 * Date: 2018/10/17
 * Time: 15:47
 */

namespace Authentication;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

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
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'index' => __DIR__ . '/../view',
        ],
    ],
];

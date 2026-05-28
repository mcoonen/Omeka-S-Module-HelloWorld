<?php
declare(strict_types=1);

// Declaring modules namespace here.  Allows us to automatically reference classes in the 
// src folder of our module with path-names relative to the src folder.
// IMPORTANT:  Concerning the directory structure of your module:  WIthin the view folder
// you will have so-called action templates.  These must be in a subfolder that splits the 
// that duplicates this namespace name, except splitting the CamelCase words with a hyphen 
// using all lower case.  In this case, we have view/hello-world .   Can't find any mention of 
// this in the Omeka-S developer docs!

namespace HelloWorld;

// Referencing these classes permits us to reference them by name without the full path. 
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    // Register form classes so Laminas can instantiate them via FormElementManager.
    // This ensures init() is called automatically and allows future injection of services.
    'form_elements' => [
        'invokables' => [
            \HelloWorld\Form\SiteSettingsFieldset::class => \HelloWorld\Form\SiteSettingsFieldset::class,
        ],
    ],

    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    'helloworld' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/helloworld[/:action]',
                            'defaults' => [
                                '__NAMESPACE__' => 'HelloWorld\Controller',
                                'controller' => Controller\HelloController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    // New Route: used to generate links, among other things.
                    'hellogreet' => [
                        'type' => Literal::class, // exact match of URI path
                        'options' => [
                            'route' => '/helloworld/greet', // URI path
                            'defaults' => [
                                '__NAMESPACE__' => 'HelloWorld\Controller',
                                'controller' => Controller\HelloController::class, // unique name
                                'action'     => 'greet',

                            ],
                        ],
                    ],
                    'weatherforecast' => [
                        'type' => Segment::class, // supports optional :action segment
                        'options' => [
                            'route' => '/weather[/:action]', // URI path; e.g. /weather or /weather/filter
                            'defaults' => [
                                '__NAMESPACE__' => 'HelloWorld\Controller',
                                'controller' => Controller\WeatherController::class, // unique name
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        // Tell the application how to instantiate our controller class
        'factories' => [
            // Add all declared Controller classes (e.g. HelloController, WeatherController, etc.)  to the array of invokable controllers.
            Controller\HelloController::class => InvokableFactory::class,
            Controller\WeatherController::class => InvokableFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
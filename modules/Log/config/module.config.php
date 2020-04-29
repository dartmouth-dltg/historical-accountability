<?php
namespace Log;

return [
    'logger' => [
        // The default config in Omeka is false, but this module is designed to log.
        'log' => true,
        // Path and priority are used by Omeka default config. Anyway, the local
        // config override it, at least for priority. To set null avoid a check.
        // Note: these options override the ones in the standard config:
        // - path: config[logger][options][writers][stream][options][stream]
        // - priority: config[logger][options][writers][stream][options][filters]
        'path' => null,
        // This is the default level in the standard config. Should not be null
        // when upgrade (so check the file config/local.config.php).
        'priority' => null,
        'writers' => [
            // The database used by this module. The database can be the main
            // one or an another one: set it "config/database-log.ini" or in
            // this file as config[logger][options][writers][db][options][db]
            // Warning: Omeka use "dbname" for "database" and "user" for "username".
            // Furthermore, you have to define the table and columns to use
            // in the options below: config[logger][options][writers][db][options].
            // Note: even disabled, the database may be used via loggerDb().
            'db' => true,
            // This is the default log file of Omeka (logs/application.log).
            'stream' => true,
            // Log for Omeka jobs (useless with this module, but kept for testing purpose).
            // This is a standard Zend writer, but there is no more parameters.
            'job' => true,
            // This is the default log for php. On a web server, it may be a log inside /var/log
            // like /var/log/nginx/ssl-vhost1.error.log, /var/log/apache2/error.log, /var/log/lastlog, or
            // /tmp/systemd-private-xxx-apache2.service-xxx/tmp/php_errors.log, etc.
            'syslog' => true,
            // Config for sentry, an error tracking service (https://sentry.io).
            // See readme to enable it.
            'sentry' => false,
            // Note: External logs (db, sentry, etc.) are not fully checked, so their
            // config should be checked separately.
        ],
        'options' => [
            'writers' => [
                'db' => [
                    'name' => 'db',
                    'options' => [
                        'filters' => \Zend\Log\Logger::INFO,
                        'formatter' => Formatter\PsrLogDb::class,
                        'db' => null,
                        // 'db' => new \Zend\Db\Adapter\Adapter([
                        //     'driver' => 'mysqli',
                        //     'database' =>null,
                        //     'username' => null,
                        //     'password' => null,
                        //     'unix_socket' => null,
                        //     'host' => null,
                        //     'port' => null,
                        //     'log_path' => null,
                        // ]),
                        'table' => 'log',
                        'column' => [
                            'priority' => 'severity',
                            'message' => 'message',
                            'timestamp' => 'created',
                            'extra' => [
                                'context' => 'context',
                                'referenceId' => 'reference',
                                'userId' => 'owner_id',
                                'jobId' => 'job_id',
                            ],
                        ],
                    ],
                ],
                'stream' => [
                    'name' => 'stream',
                    'options' => [
                        // This is the default level in the standard config.
                        'filters' => \Zend\Log\Logger::NOTICE,
                        'formatter' => Formatter\PsrLogSimple::class,
                        'stream' => OMEKA_PATH . '/logs/application.log',
                    ],
                ],
                'syslog' => [
                    'name' => 'syslog',
                    'options' => [
                        'filters' => \Zend\Log\Logger::ERR,
                        'formatter' => Formatter\PsrLogSimple::class,
                        'application' => 'omeka-s',
                        'facility' => LOG_USER,
                    ],
                ],
                // See https://github.com/facile-it/sentry-module#log-writer
                'sentry' => [
                    'name' => \Facile\SentryModule\Log\Writer\Sentry::class,
                    'options' => [
                        'filters' => [
                            [
                                'name' => 'priority',
                                'options' => [
                                    'priority' => \Zend\Log\Logger::INFO,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'processors' => [
                'userid' => [
                    'name' => Processor\UserId::class,
                ],
            ],
            // Special options for exceptions, errors and fatal errors, disabled by Zend by default.
            // Note that it may disable the default error logging of php and debug tools.
            // 'exceptionhandler' => true,
            // 'errorhandler' => true,
            // 'fatal_error_shutdownfunction' => true,
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'logs' => Api\Adapter\LogAdapter::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
        'filters' => [
            'log_visibility' => Db\Filter\LogVisibilityFilter::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'logSearchFilters' => View\Helper\LogSearchFilters::class,
            // Required to manage PsrMessage.
            'messages' => View\Helper\Messages::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\QuickSearchForm::class => Service\Form\QuickSearchFormFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\Admin\LogController::class => Service\Controller\Admin\LogControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'loggerDb' => Service\ControllerPlugin\LoggerDbFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Log\LoggerDb' => Service\LoggerDbFactory::class,
            'Omeka\Job\Dispatcher' => Service\Job\DispatcherFactory::class,
            'Omeka\Job\DispatchStrategy\Synchronous' => Service\Job\DispatchStrategy\SynchronousFactory::class,
            'Omeka\Logger' => Service\LoggerFactory::class,
        ],
    ],
    'log_processors' => [
        'invokables' => [
            Processor\JobId::class => Processor\JobId::class,
        ],
        'factories' => [
            Processor\UserId::class => Service\Processor\UserIdFactory::class,
        ],
        'aliases' => [
            'jobid' => Processor\JobId::class,
            'userid' => Processor\UserId::class,
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'log' => [
                        'type' => \Zend\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/log',
                            'defaults' => [
                                '__NAMESPACE__' => 'Log\Controller\Admin',
                                'controller' => Controller\Admin\LogController::class,
                                'action' => 'browse',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type' => \Zend\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/:action',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                            'id' => [
                                'type' => \Zend\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/:id[/:action]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' => '\d+',
                                    ],
                                    'defaults' => [
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'AdminGlobal' => [
            [
                'label' => 'Logs', // @translate
                'class' => 'fa-list',
                'route' => 'admin/log',
                'resource' => Controller\Admin\LogController::class,
                'privilege' => 'browse',
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
];

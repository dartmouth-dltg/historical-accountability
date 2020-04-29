Log (module for Omeka S)
========================

[Log] is a module for [Omeka S] that allows to monitor all logging messages and
background jobs directly in the admin board, in syslog, or in cloud services via
third parties and make them easily checkable.

Furthermore, additionnal logging destinations (alternative monitor, custom
logging…) and behaviors can be set just by providing their config, for example
to send an email when a critical error occurs.

The logs are [PSR-3] compliant: they can managed by any other tool that respects
this standard (see below). They can be translated too.


Installation
------------

The module uses an external library, [`webui-popover`], so use the release zip
to install it, or use and init the source.

See general end user documentation for [installing a module].

* From the zip

Download the last release [`Log.zip`] from the list of releases (the master does
not contain the dependency), and uncompress it in the `modules` directory.

* From the source and for development

If the module was installed from the source, rename the name of the folder of
the module to `Log`, go to the root module, and run:

```
    composer install
```

* Upgrade from Omeka 1.4 to Omeka 2.0

If an issue appears after upgrade of Omeka, don‘t forget to update the packages
of Omeka: `rm -rf vendor && composer install --no-dev`.


Config
------

The config is a pure Zend log config: see the [Zend Framework Log] documentation
for the format. Only common settings are explained here.

To enable or disable an option or a writer, it is recommended to copy the wanted
keys inside your own `config/local.config.php`, so the maintenance will be
simpler.

The default config allows to keep existing log mechanisms: the file `logs/application.log`
and the background logs in the table `job` inside the Omeka database. They can
be disabled if wanted.

The logger allows to define one or more of writers (a file, a database, a cloud
service, syslog, etc.). All the writers are listed in `config['logger']['writers']`.
When enable, a writer take its own config in the `config['logger']['options']['writers']`.
See the example in the [config of the module].

Note: External logs (db, sentry, etc.) are not fully checked for performance
reasons, and may fail silently, so their config should be checked separately.

### Default logs

After testing the module, if you want to disable double logging (stream for
direct logging and Omeka database for background jobs), add these keys in your
own `config/local.config.php`:

```php
    'logger' => [
        'log' => true,
        'writers' => [
            'stream' => false,
            'job' => false,
        ],
    ],
```

Instead, you can set a different severity level for database logging and file
logging (this example shows the default levels):

```php
    'logger' => [
        'log' => true,
        'writers' => [
            'stream' => false,
            'job' => false,
            'syslog' => true,
        ],
        'options' => [
            'writers' => [
                'db' => [
                    'options' => [
                        'filters' => \Zend\Log\Logger::INFO,
                    ],
                ],
                'stream' => [
                    'options' => [
                        'filters' => \Zend\Log\Logger::NOTICE,
                    ],
                ],
                'syslog' => [
                    'options' => [
                        'filters' => \Zend\Log\Logger::ERR,
                    ],
                ],
            ],
        ],
    ],
```

### Background job logs

The job logs are automatically saved in the database and manageable in the admin
interface.

The default job logging is still enabled by default in config. To disable it,
set the key `['logger']['writers']['job']` to false in your own `config/local.config.php`:

```php
    'logger' => [
        'writers' => [
            'job' => false,
        ],
    ],
```

### Php errors and exceptions

By default, exceptions that are not managed by Omeka and php errors are logged
only in the file `php_errors.log` of the server. To enable them inside the
logger, add the options, at your choice:

```php
    'logger' => [
        'options' => [
            'exceptionhandler' => true,
            'errorhandler' => true,
            'fatal_error_shutdownfunction' => true,
        ],
    ],
```

Note that this will disable the default error logging of php and debug tools, so
if you want to keep it, add a writer for it.

Furthermore, they are managed automatically for background jobs.

### External database

The logs can be saved in an external database. To config it, add a file
`database-log.ini` beside the main `database.ini` of Omeka S, with its params,
and the params of the table inside `config['logger']['options']['writers']['db']['options']`.
Warning: for technical reasons, Omeka use `dbname` and `user`, but Zend uses
`database` and `username`:

```ini
username = ""
password = ""
database = ""
host     = ""
;port     =
;unix_socket =
;driver   =
```

Note that when the logs are logged externally, the admin interface cannot be
used.

### Additionnal logging

Other logging can be added. Just add their config in your `['logger']['options']`
and enable them under the key `['logger']['writers']`. See the [Zend Framework Log]
documentation for the format of the config.

### Sentry

[Sentry] is an error tracking service. It should be installed in a particular
way, following these steps, from the root of Omeka S:

- Sentry requires the library `php-curl`, that should be enabled on the server.
- Sentry should be installed via composer in the root of Omeka, with platform
  php = 7.0 instead of platform php = 5.6, so update it in `composer.json`. If
  not, an old version of Sentry will be used, that doesn't work with other
  dependencies.
- Include the library:

```bash
composer require facile-it/sentry-module
```

- The psr formatter `facile-it/sentry-psr-log` may be added too (need config).
- Copy the default config file (see // https://github.com/facile-it/sentry-module#client),
  and set your Sentry dsn:

```bash
cp modules/Log/config/sentry.config.local.php.dist config/sentry.config.local.php
sed -i -r "s|'dsn' => '',|'dsn' => 'https://abcdefabcdefabcdefabcdefabcdefab@sentry.io/1234567',|" config/sentry.config.local.php
```

In the file `application/config/application.config.php`, add the module
`Facile\SentryModule` as the last module, plus the config file as new config_glob_paths
of module_listener_options:

```php
return [
    'modules' => [
        'Zend\Form',
        'Zend\I18n',
        'Zend\Mvc\I18n',
        'Zend\Mvc\Plugin\Identity',
        'Zend\Navigation',
        'Zend\Router',
        'Omeka',
        'Facile\SentryModule',
    ],
    'module_listener_options' => [
        'module_paths' => [
            'Omeka' => OMEKA_PATH . '/application',
            OMEKA_PATH . '/modules',
        ],
        'config_glob_paths' => [
            OMEKA_PATH . '/config/local.config.php',
            OMEKA_PATH . '/config/sentry.config.local.php',
        ],
    […]
```

Finally, enable Sentry via your `config/local.config.php`:

```php
        'writers' => [
            'sentry' => true,
        ],
```

That's all!


PSR-3 and logging
-----------------

### PSR-3

The PHP Framework Interop Group ([PHP-FIG]) represents the majority of php
frameworks, in particular all main CMS.

[PSR-3] means that the message and its context may be separated in the logs, so
they can be translated and managed by any other compliant tools. This is useful
in particular when an external database is used to store logs.

The message uses placeholders that are not in C-style of the function `sprintf`
(`%s`, `%d`, etc.), but in moustache-style, identified with `{` and `}`, without
spaces.

So, instead of logging like this:

```php
// Classic logging.
$this->logger()->info(sprintf($message, ...$args));
$this->logger()->info(sprintf('The %s #%d has been updated.', 'item', 43));
// output: The item #43 has been updated.
```

a PSR-3 standard log is:

```php
// PSR-3 logging.
$this->logger()->info($message, $context);
$this->logger()->info(
    'The {resource} #{id} has been updated.', // @translate
    ['resource' => 'item', 'id' => 43]
);
// output: The item #43 has been updated.
```

If an Exception object is passed in the context data, it must be in the `exception`
key.

Because the logs are translatable at user level, with a message and context, the
message must not be translated when logging.

### Logging extra data

The module adds three extra data to improve management of logs inside Omeka: the
current user, the job and a reference. The user and the job are automatically
added via the extra keys `userId` and `jobId`, that replace manually set keys.
The reference can be added as additional key `referenceId`. If the context uses
these keys as placeholders, they are mapped in the message, else they are
removed from the context.

```php
// PSR-3 logging with extra data.
$this->logger()->info(
    'The {resource} #{id} has been updated by user #{userId}.', // @translate
    ['resource' => 'item', 'id' => 43, 'referenceId' => 'curation']
);
// output in stream: The item #43 has been updated by user #1. {"referenceId":"curation"}
// output in database: The item #43 has been updated by user #1.
```

In this implementation, like the default Zend stream logger, extra data that are
not mappable are json encoded and appended to the end of the message via the key
`{extra}`. So this key should not be used in the context when there are
non-mapped keys.

```php
// PSR-3 logging with non-mappable extra data.
$this->logger()->info(
    'The {resource} #{id} has been updated by user #{userId}.', // @translate
    ['resource' => 'item', 'id' => 43, 'referenceId' => 'curation', 'pid' => 1234]
);
// output in stream: The item #43 has been updated by user #1. {"pid":1234,"referenceId"="curation"}
// output in database: The item #43 has been updated by user #1. {"pid":1234}
```

The reference can be any short string. It may be a category or a unique
identifier. If there is a job, it may repeat or not the values available in the
job settings and metadata.

It can be added at the beginning of the process to avoid to set it for each log:

```php
// PSR-3 logging with reference id (a random number if not set).
$referenceIdProcessor = new \Zend\Log\Processor\ReferenceId();
$referenceIdProcessor->setReferenceId('bulk/import/27');
$this->logger()->addProcessor($referenceIdProcessor);
$this->logger()->info(
    'The {resource} #{id} has been updated by user #{userId}.', // @translate
    ['resource' => 'item', 'id' => 43]
);
// output in stream: The item #43 has been updated by user #1. {"referenceId":"MyModule: my-process"}
// output in database: The item #43 has been updated by user #1.
```

### Compatibility

* Compatibility with the default stream logger

The PSR-3 messages are converted into simple messages for the default logger.
Other extra data are appended.

* Compatibility with core messages

The logger stores the core messages as it, without context, so they can be
displayed. They are not translatable if they use placeholders.

### Helpers

- Direct database logging

A controller plugin is available to log messages directly in the database and
inside it only: `loggerDb`. If used inside a job, it should be initialized to
keep track of the user and the job:

```php
$userJobIdProcessor = new \Log\Processor\UserJobId($this->job);
$this->loggerDb()->addProcessor($userJobIdProcessor);
```

- PSR-3 Message

If the message may be reused, the helper PsrMessage() can be used, with all the
values:

```php
$message = new \Log\Stdlib\PsrMessage(
    'The {resource} #{id} has been updated by user #{userId}.', // @translate
    ['resource' => 'item', 'id' => 43, 'userId' => $user->id()]
);
$this->logger()->info($message->getMessage(), $message->getContext());
echo $message;
// With translation.
$message->setTranslator($translator);
echo $message;
```

- Plural

By construction, the plural is not managed: only one message is saved in the
log. So, if any, the plural message should be prepared before the logging.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitHub.


License
-------

This module is published under the [CeCILL v2.1] licence, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.

* The library [`webui-popover`] is published under the license [MIT].
* The library [`facile/sentry`] is published under the license [MIT].


Copyright
---------

* Copyright Daniel Berthereau, 2017-2019 [Daniel-KM] on GitHub)

* Library [`webui-popover`]: Sandy Walker
* Library [`facile/sentry`]: Copyright 2016 Thomas Mauro Vargiu


[Log]: https://github.com/Daniel-KM/Omeka-S-module-Log
[Omeka S]: https://omeka.org/s
[PSR-3]: http://www.php-fig.org/psr/psr-3
[PHP-FIG]: http://www.php-fig.org
[`webui-popover`]: https://github.com/sandywalker/webui-popover
[installing a module]: http://dev.omeka.org/docs/s/user-manual/modules/#installing-modules
[`Log.zip`]: https://github.com/Daniel-KM/Omeka-S-module-Log/releases
[Zend Framework Log]: https://docs.zendframework.com/zend-log
[config of the module]: https://github.com/Daniel-KM/Omeka-S-module-Log/blob/master/config/module.config.php#L5-L115
[Sentry]: https://sentry.io
[`facile/sentry`]: https://github.com/facile-it/sentry-module
[module issues]: https://github.com/Daniel-KM/Omeka-S-module-Log/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[MIT]: https://github.com/sandywalker/webui-popover/blob/master/LICENSE.txt
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"

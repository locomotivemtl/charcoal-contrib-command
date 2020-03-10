Charcoal Contrib Command
===============

[![License][badge-license]][charcoal-contrib-command]
[![Latest Stable Version][badge-version]][charcoal-contrib-command]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

A charcoal contrib used to provide dynamic cron scripts, easily schedulable.


## Table of Contents

-   [Installation](#installation)
    -   [Dependencies](#dependencies)
-   [Service Provider](#service-provider)
    -   [Services](#services)
-   [Configuration](#configuration)
    -   [Defaults](#defaults)
-   [Usage](#usage)
    -   [Creating a command class](#creating-a-command-class)
        -   [Interface](#interface)
        -   [Example](#example)
    -   [Back-end form](#back-end-form)
    -   [Available services](#available-services)
        -   [Method](#method)
        -   [Parameters](#parameters)
-   [Development](#development)
    -  [API Documentation](#api-documentation)
    -  [Development Dependencies](#development-dependencies)
    -  [Coding Style](#coding-style)
-   [Credits](#credits)
-   [License](#license)



## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-contrib-command
```

### Including the modules

Just add the following module in your json configuration file:

`"charcoal/command/command": {},`

### Setting a cron

Script `command/process-queue` is added with the command's module.

```cli
* * * * * export APPLICATION_ENV=lab && /usr/local/bin/php /home/lab/www/alerts/otterburn-park/v4/vendor/bin/charcoal messaging/sms/process-queue > /dev/null 2>&1
```


### Dependencies

#### Required

-   [**PHP 7.1+**](https://php.net): _PHP 7.3+_ is recommended.
-   [**Monolog/monolog**](https://github.com/Seldaek/monolog): Official project logger.


#### PSR

--TBD--


## Service Provider

The following services are provided with the use of [charcoal-contrib-command][charcoal-contrib-command]

### Services

- **command/queue-stack**: instance of Command\Service\CommandQueuer


## Configuration

Every bit of configuration for the command contrib should be under the namespace `command`.
Configurations allows the possibility to define options for the logger, such as the handlers, processors or formatters.
All default configurations are visible in the `Charcoal\Command\Logger\Config\CommandLoggerConfig` class.

### Defaults
```json
    "command": {
        "logger": {
            "level": "debug",
            "active": true,
            "handlers": {
                "charcoal/command/logger/handler/charcoal": {
                    "model": "charcoal/command/log",
                    "formatter": {
                        "charcoal/command/logger/formatter/command": {}
                    }
                }
            }
        }
    }
```
The handlers should be resolvable by the logger handler generic factory. In the given example, we see `Charcoal\Command\Logger\Handler\CharcoalHandler` class.
The options defined with the handler will be passed in the handler's constructor method.

The formatters should be resolvable by the logger formatter generic factory. In the given example, we see `Charcoal\Command\Logger\Formatter\Command`.
The options defined with the formatter will be passed in the formatter's constructor method.

The class should be resolvable by the model factory. In the given example, we see `Charcoal\Command\Log`.


## Usage

### Creating a command class

A command class should extend the `AbstractCommand` class.
You can use the `setDependencies` public method to access any dependencies you might need. The public method
`execute` is called when everything is ready.

#### Interface
- public **AbstractCommand::__invoke ( array $arguments )**: Sets arguments, execute command and log results
- public **AbstractCommand::setDependencies ( Container $container )**: Allows to set any required dependencies
- protected **AbstractCommand::log ()**: Called upon execution. Logs according to the given options.
- protected **AbstractCommand::setSuccess ( boolean $success )**: Defines if the scripts was executed successfully or not.
- abstract protected **AbstractCommand::execute ()**: The actual command goes in there.

#### Example
```php

namespace Charcoal\Cache\Command;

[...]

/**
 * Cache clearer command
 */
class ClearCommand extends AbstractCommand
{
    use CachePoolAwareTrait;

    /**
     * @param array $arguments
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->setCachePool($container['cache']);
    }

    /**
     * @return mixed|void
     */
    public function execute()
    {
        $success = $this->cachePool()->clear();
        $this->setSuccess($success);
    }
}
```

### Back-end form

Use the CommandQueue model to queue a new command.

| Key               | Type          | Example                           | Description       
|:---:              |:---:          |:---:                              |:---:
| `command`         | string        | charcoal/cache/command/clear      | Should be resolvable by the command factory. Hits `Charcoal\Cache\Command\ClearCommand`
| `arguments`       | array         | { "someProperty": "someValue" }   | Any arguments that could be necessary for the executed command. Passed as argument to the command's constructor method.
| `issuedDate`      | DateTime      | now                               | When was the command issued. This is done on preSave and shouldn't be altered.
| `processingDate`  | DateTime      | now                               | When should the script be run?
| `processedDate`   | DateTime      | now                               | When has the script been ran? Could differ from the processingDate depending on the cron frenquency and the time of execution of the command.
| `processed`       | boolean       | false                             | Flag as to whether or not the command has been processed.


### Available Service

#### Method
```
public QueueStack::enqueue ( array $data ) : QueueStack
```

#### Parameters
Array **data**

- `command`: Required. Resolvable Command class string.
- `processingDate`: Optional. Date when the command should be run. Accept any valid DateTime format or a DateTime object. Defaults to `now`
- `arguments`: Options. Array of arguments to be passed to the command constructor method.

##### Example
```php
    $stack = $container['command/queue-stack'];
    $stack->enqueue([
        'command' => 'charcoal/cache/command/clear',
        'processingDate' => 'NOW +1 day'
    ]);
```

The default processing date is "now". 


## Development

To install the development environment:

```shell
$ composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
$ composer test
```


### API Documentation

-   The auto-generated `phpDocumentor` API documentation is available at:  
    [https://locomotivemtl.github.io/charcoal-contrib-command/docs/master/](https://locomotivemtl.github.io/charcoal-contrib-command/docs/master/)
-   The auto-generated `apigen` API documentation is available at:  
    [https://codedoc.pub/locomotivemtl/charcoal-contrib-command/master/](https://codedoc.pub/locomotivemtl/charcoal-contrib-command/master/index.html)



### Development Dependencies

-   [php-coveralls/php-coveralls][phpcov]
-   [phpunit/phpunit][phpunit]
-   [squizlabs/php_codesniffer][phpcs]



### Coding Style

The charcoal-contrib-command module follows the Charcoal coding-style:

-   [_PSR-1_][psr-1]
-   [_PSR-2_][psr-2]
-   [_PSR-4_][psr-4], autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   [phpcs.xml.dist](phpcs.xml.dist) and [.editorconfig](.editorconfig) for coding standards.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.



## Credits

-   [Locomotive](https://locomotive.ca/)



## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.



[charcoal-contrib-command]:  https://packagist.org/packages/locomotivemtl/charcoal-contrib-command
[charcoal-app]:             https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-contrib-command/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-contrib-command
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-contrib-command

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-contrib-command.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-contrib-command.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-contrib-command.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-contrib-command.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-contrib-command.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
[psr-12]: https://www.php-fig.org/psr/psr-12/

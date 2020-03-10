<?php

namespace Charcoal\Command\ServiceProvider;

use Charcoal\Command\Factory\CommandFactory;
use Charcoal\Command\Logger\Config\CommandLoggerConfig;
use Charcoal\Command\Service\QueueStack;
use Charcoal\Factory\GenericFactory;
use InvalidArgumentException;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;


/**
 * Charcoal Command Service Provider
 */
class CommandServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  Container $container Service locator.
     * @return void
     */
    public function register(Container $container)
    {
        $this->registerFactories($container);
        $this->registerLogger($container);
        $this->registerServices($container);
    }

    /**
     * Command services
     *
     *
     * @param Container $container
     * @return void
     */
    public function registerServices(Container $container)
    {
        /**
         * Queue helper
         *
         * @param Container $container
         * @return QueueStack
         */
        $container['command/queue-stack'] = function (Container $container) {
            return new QueueStack([
                'model/factory' => $container['model/factory']
            ]);
        };
    }

    /**
     * Command factories
     *
     *
     * @param Container $container
     * @return void
     */
    public function registerFactories(Container $container)
    {
        /**
         * @param Container $container
         * @return CommandFactory
         */
        $container['command/factory'] = function (Container $container) {
            return new CommandFactory([
                'resolver_options' => [
                    'suffix' => 'Command'
                ],
                'arguments'        => ['container' => $container]
            ]);
        };

        /**
         * @param Container $container
         * @return GenericFactory
         */
        $container['logger/handler/factory'] = function (Container $container) {
            return new GenericFactory([
                'resolver_options' => [
                    'suffix' => 'Handler'
                ]
            ]);
        };

        /**
         * @param Container $container
         * @return GenericFactory
         */
        $container['logger/formatter/factory'] = function (Container $container) {
            return new GenericFactory([
                'resolver_options' => [
                    'suffix' => 'Formatter'
                ]
            ]);
        };

        /**
         * @param Container $container
         * @return GenericFactory
         */
        $container['logger/processor/factory'] = function (Container $container) {
            return new GenericFactory([
                'resolver_options' => [
                    'suffix' => 'Processor'
                ]
            ]);
        };
    }

    /**
     * Register command logger using Monolog/Logger
     *
     * @param Container $container
     */
    public function registerLogger(Container $container)
    {
        /**
         * @param Container $container
         * @return CommandLoggerConfig
         */
        $container['command/logger/config'] = function (Container $container) {
            $config = $container['config'];

            return new CommandLoggerConfig($config->get('command.logger'));
        };

        /**
         * @param Container $container
         */
        $container['command/logger'] = function (Container $container) {

            $logger           = new Logger('command');
            $config           = $container['command/logger/config'];
            $handlerFactory   = $container['logger/handler/factory'];
            $processorFactory = $container['logger/processor/factory'];


            if (!$config['active']) {
                return new NullLogger();
            }

            $handlers = $config['handlers'];
            if (count($handlers)) {
                foreach ($handlers as $handleKey => $opts) {
                    try {
                        $handler = $handlerFactory->create($handleKey, [
                            'options'   => $opts,
                            'config'    => $config,
                            'container' => $container
                        ]);
                        $logger->pushHandler($handler);
                    } catch (InvalidArgumentException $e) {
                        // Fail silently
                    }
                }
            }

            $processors = $config['processors'];
            if (count($processors)) {
                foreach ($processors as $processorKey => $opts) {
                    try {
                        $processor = $processorFactory->create($processorKey, [
                            'options'   => $opts,
                            'config'    => $config,
                            'container' => $container
                        ]);
                        $logger->pushProcessor($processor);
                    } catch (InvalidArgumentException $e) {
                        // Fail silently
                    }
                }
            }

            return $logger;
        };

    }
}

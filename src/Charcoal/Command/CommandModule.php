<?php

namespace Charcoal\Command;

use Charcoal\App\Module\AbstractModule;
use Charcoal\Command\ServiceProvider\CommandServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CommandModule extends AbstractModule
{
    /**
     * Setup the module's dependencies.
     *
     * @return self
     */
    public function setup()
    {
        $container = $this->app()->getContainer();

        $commandServiceProvider = new CommandServiceProvider();
        $container->register($commandServiceProvider);

        $helpConfig = $container['admin/guide/config'];
        $this->setConfig($helpConfig);

        $routeIdent = 'charcoal/command/script/process-queue';
        $methods = ['GET'];
        $routePattern = '/command/process-queue';

        $scriptConfig = [
            'ident' => $routeIdent,
            'methods' => $methods,
            'script_data' => []
        ];

        $this->app()->map(
            $methods,
            $routePattern,
            function (
                RequestInterface $request,
                ResponseInterface $response,
                array $args = []
            ) use (
                $routeIdent,
                $scriptConfig
            ) {
                if (count($args)) {
                    $scriptConfig['script_data'] = array_merge(
                        $scriptConfig['script_data'],
                        $args
                    );
                }

                $defaultController = $this['route/controller/script/class'];
                $routeController   = isset($scriptConfig['route_controller'])
                    ? $scriptConfig['route_controller']
                    : $defaultController;

                $routeFactory = $this['route/factory'];
                $routeFactory->setDefaultClass($defaultController);

                $route = $routeFactory->create($routeController, [
                    'config' => $scriptConfig,
                    'logger' => $this['logger']
                ]);
                return $route($this, $request, $response);
            }
        );

        return $this;
    }
}

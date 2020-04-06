<?php

namespace Charcoal\Command\Script;

use Charcoal\App\Script\AbstractScript;
use Charcoal\Factory\FactoryInterface;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Destructively Alter SQL Table
 */
class RunCommandScript extends AbstractScript
{
    /**
     * @var FactoryInterface
     */
    protected $commandFactory;

    /**
     * Give an opportunity to children classes to inject dependencies from a Pimple Container.
     *
     * Does nothing by default, reimplement in children classes.
     *
     * The `$container` DI-container (from `Pimple`) should not be saved or passed around, only to be used to
     * inject dependencies (typically via setters).
     *
     * @param  Container $container Service locator.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        $this->setCommandFactory($container['command/factory']);
        parent::setDependencies($container);
    }

    /**
     * @return FactoryInterface
     */
    public function commandFactory()
    {
        return $this->commandFactory;
    }

    /**
     * @param FactoryInterface $commandFactory
     * @return ProcessCommandScript
     */
    public function setCommandFactory(FactoryInterface $commandFactory)
    {
        $this->commandFactory = $commandFactory;
        return $this;
    }

    /**
     * Retrieve the script's supported arguments.
     *
     * @return array
     */
    public function defaultArguments()
    {
        return [
            'command' => [
                'prefix'      => 'c',
                'longPrefix'  => 'command',
                'description' => 'Command to run.'
            ],
            'arguments' => [
                'prefix'      => 'a',
                'longPrefix'  => 'arguments',
                'description' => 'Arguments (multiple). Format should be -a ident=value'
            ]
        ];
    }

    /**
     * Run the script.
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        // Unused
        unset($request);

        // Get arguments as array. Comes from -a ident=value -a ident2=value2 -a [...]
        $arguments = $this->climate()->arguments->getArray('arguments');
        if (!empty($arguments)) {
            $arguments = $this->buildArguments($arguments);
        }

        // The desired command
        $command = $this->argOrInput('command');

        try {
            $execute = $this->commandFactory()->create($command);
            $execute($arguments);
        } catch (\InvalidArgumentException $e) {
            // Fail silently
            // If it fails, it should be because the commandFactory couldn't resolve the command.
        }

        return $response;
    }

    /**
     * @param $arguments
     * @return array
     */
    private function buildArguments($arguments) {
        $out = [];
        foreach ($arguments as $arg) {
            $split = explode('=', $arg);
            if (count($split) !== 2) {
                continue;
            }
            $out[$split[0]] = $split[1];
        }

        return $out;
    }
}

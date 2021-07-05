<?php

namespace Charcoal\Command\Script;

use Charcoal\App\Script\AbstractScript;
use Charcoal\Command\CommandQueue;
use Charcoal\Factory\FactoryInterface;
use Charcoal\Loader\CollectionLoaderAwareTrait;
use Charcoal\Model\ModelFactoryTrait;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Destructively Alter SQL Table
 */
class ProcessQueueScript extends AbstractScript
{
    use ModelFactoryTrait;
    use CollectionLoaderAwareTrait;

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
        $this->setModelFactory($container['model/factory']);
        $this->setCollectionLoader($container['model/collection/loader']);
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

        $this->climate()->arguments->parse();

        $loader = $this->collectionLoader();
        $proto  = $this->modelFactory()->create(CommandQueue::class);
        $loader->setModel($proto)
            ->addFilter('objTable.processed = 0')
            ->addFilter('objTable.processing_date <= NOW()')
            ->addOrder('processingDate', 'asc');

        $queues = $loader->load();

        if (!count($queues)) {
            $this->climate()->error()->out('No command in queue');
            return $response;
        }

        foreach ($queues as $q) {
            try {
                $command   = $this->commandFactory()->create($q->getCommand());
                $arguments = json_decode($q->getArguments(), true);
                $command($arguments);
            } catch (\InvalidArgumentException $e) {
                // Fail silently
                // If it fails, it should be because the commandFactory couldn't resolve the command.
            }

            // Update command queue anyway.
            $q->setProcessed(true);
            $q->setProcessedDate('now');
            $q->update();
        }

        return $response;
    }
}

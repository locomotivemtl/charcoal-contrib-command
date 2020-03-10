<?php

namespace Charcoal\Command;

use Pimple\Container;

interface CommandInterface
{
    /**
     * Invoke magic method
     *
     * @param array     $arguments
     * @return mixed
     */
    public function __invoke($arguments=[]);

    /**
     * @param Container $container
     * @return mixed
     */
    public function setDependencies(Container $container);
}

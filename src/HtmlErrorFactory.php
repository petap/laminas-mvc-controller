<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class HtmlErrorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        $viewModel = $container->get('petap-laminas-mvc-view-model-factory');

        return $error = new HtmlError($app->getMvcEvent(), $viewModel);
    }
}

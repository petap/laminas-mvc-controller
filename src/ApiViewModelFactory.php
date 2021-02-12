<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\View\Model\JsonModel;

class ApiViewModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        if ($routeMatch->getParam('viewModel')) {
            $viewModel = $container->get($routeMatch->getParam('viewModel'));
            if (! $viewModel instanceof JsonModel) {
                throw new \RuntimeException('ViewModel must be instance of ' . JsonModel::class);
            }
        } else {
            $viewModel = new JsonModel();
        }

        return $viewModel;
    }
}

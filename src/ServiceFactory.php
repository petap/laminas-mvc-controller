<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\EmptyService;
use PetapDomainInterface\ServiceInterface;

class ServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        if ($routeMatch->getParam('service')) {
            $service = $container->get($routeMatch->getParam('service'));
            if (!$service instanceof ServiceInterface) {
                throw new \RuntimeException('Service must be instance of ' . ServiceInterface::class);
            }
        } else {
            $service = new EmptyService();
        }

        return $service;
    }
}

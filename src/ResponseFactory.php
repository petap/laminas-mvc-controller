<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\ResponseInterface;
use Petap\Controller\Response;

class ResponseFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        if ($routeMatch->getParam('response')) {
            $response = $container->get($routeMatch->getParam('response'));
            if (! $response instanceof ResponseInterface) {
                throw new \RuntimeException('Response must be instance of ' . ResponseInterface::class);
            }
        } else {
            $response = new Response();
        }

        $redirectTo = $routeMatch->getParam('redirectTo');
        if (!empty($redirectTo) && !is_string($redirectTo)) {
            throw new \RuntimeException('Parameter redirectTo must be string');
        }

        $response->setRedirectTo($redirectTo);

        return $response;
    }
}

<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\RequestInterface;
use Petap\Controller\Request;

class RequestFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        /** @var \Laminas\Http\PhpEnvironment\Request $laminasRequest */
        $laminasRequest = $container->get('request');

        /** @var RequestInterface $request */
        if ($routeMatch->getParam('request')) {
            $request = $container->get($routeMatch->getParam('request'));
            if (! $request instanceof RequestInterface) {
                throw new \RuntimeException('Request must be instance of ' . RequestInterface::class);
            }
        } else {
            $request = new Request();

            $routeMatchParams = $routeMatch->getParams();
            $routeMatchCriteria = [];
            if (!empty($routeMatchParams['routeCriteria'])) {
                if (is_string($routeMatchParams['routeCriteria'])) {
                    $routeMatchCriteria[$routeMatchParams['routeCriteria']] = $routeMatchParams[$routeMatchParams['routeCriteria']];
                }
                if (is_array($routeMatchParams['routeCriteria'])) {
                    foreach ($routeMatchParams['routeCriteria'] as $criteria) {
                        if (array_key_exists($criteria, $routeMatchParams)) {
                            $routeMatchCriteria[$criteria] = $routeMatchParams[$criteria];
                        }
                    }
                }
            }

            $criteria = array_merge($routeMatchCriteria, $laminasRequest->getQuery()->toArray());
            $request->setCriteria($criteria);

            $changes = array_merge($laminasRequest->getPost()->toArray(), $laminasRequest->getFiles()->toArray());
            $request->setChanges($changes);
        }
        $request->setMethod($laminasRequest->getMethod());

        return $request;
    }
}

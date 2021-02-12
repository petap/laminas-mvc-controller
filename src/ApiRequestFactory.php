<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\RequestInterface;
use Petap\Controller\Request;

class ApiRequestFactory implements FactoryInterface
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
            unset($routeMatchParams['controller']);
            unset($routeMatchParams['allowedMethods']);
            unset($routeMatchParams['criteriaValidator']);
            unset($routeMatchParams['changesValidator']);
            unset($routeMatchParams['service']);
            unset($routeMatchParams['template']);
            unset($routeMatchParams['viewModel']);
            unset($routeMatchParams['redirectTo']);

            $criteria = array_merge($routeMatchParams, $laminasRequest->getQuery()->toArray());
            $request->setCriteria($criteria);

            $changes = [];
            if ($laminasRequest->isPost()) {
                /** @var \Laminas\Http\Header\ContentType $contentType */
                $contentType = $laminasRequest->getHeaders()->get('content-type');
                if ($contentType) {
                    if ($contentType->getMediaType() == 'multipart/form-data') {
                        $changes = array_merge($laminasRequest->getPost()->toArray(), $laminasRequest->getFiles()->toArray());
                    } elseif ($contentType->getMediaType() == 'application/json') {
                        $changes = array_merge(
                            json_decode($laminasRequest->getContent(), true),
                            $laminasRequest->getFiles()->toArray()
                        );
                    }
                }
            }
            $request->setChanges($changes);
        }
        $request->setMethod($laminasRequest->getMethod());

        return $request;
    }
}

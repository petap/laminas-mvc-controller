<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\RequestFactory;
use Petap\Controller\RequestInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class RequestFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $laminasRequest = $this->prophesize('\Laminas\Http\PhpEnvironment\Request');
        $request = $this->prophesize(RequestInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());
        $serviceLocator->get('request')->willReturn($laminasRequest->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('request')->willReturn('Some\Request');
        $serviceLocator->get('Some\Request')->willReturn($request->reveal());

        $laminasRequest->getMethod()->willReturn('GET');
        $request->setMethod('GET')->willReturn(null);

        $factory = new RequestFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($request->reveal(), $service);
    }

    public function testCreateServiceWithDefaultRequest()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $laminasRequest = $this->prophesize('\Laminas\Http\PhpEnvironment\Request');

        $serviceLocator->get('Application')->willReturn($app->reveal());
        $serviceLocator->get('request')->willReturn($laminasRequest->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('request')->willReturn(null);
        $routeMatch->getParams()->willReturn([
            'id' => '1',
            'routeCriteria' => 'id',
        ]);

        $parameters = $this->prophesize('\Laminas\Stdlib\ParametersInterface');
        $parameters->toArray()->willReturn([]);
        $laminasRequest->getQuery()->willReturn($parameters->reveal());
        $laminasRequest->getPost()->willReturn($parameters->reveal());
        $laminasRequest->getFiles()->willReturn($parameters->reveal());

        $laminasRequest->getMethod()->willReturn('GET');

        $factory = new RequestFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(RequestInterface::class, $service);
        $this->assertEquals(['id' => '1'], $service->getCriteria());
    }
}

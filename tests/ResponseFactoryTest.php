<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ResponseFactory;
use Petap\Controller\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ResponseFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $response = $this->prophesize(ResponseInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('response')->willReturn('Some\Response');
        $routeMatch->getParam('redirectTo')->willReturn(null);
        $serviceLocator->get('Some\Response')->willReturn($response->reveal());

        $factory = new ResponseFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($response->reveal(), $service);
    }
}

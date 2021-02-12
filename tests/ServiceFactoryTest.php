<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ServiceFactory;
use PetapDomainInterface\ServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ServiceFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $service = $this->prophesize(ServiceInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('service')->willReturn('Some\Service');
        $serviceLocator->get('Some\Service')->willReturn($service->reveal());

        $factory = new ServiceFactory();

        $resultService = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($service->reveal(), $resultService);
    }
}

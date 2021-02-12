<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ApiViewModelFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ApiViewModelFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $viewModel = $this->prophesize('Laminas\View\Model\JsonModel');

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('viewModel')->willReturn('Some\ViewModel');
        $serviceLocator->get('Some\ViewModel')->willReturn($viewModel->reveal());

        $factory = new ApiViewModelFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($viewModel->reveal(), $service);
    }
}

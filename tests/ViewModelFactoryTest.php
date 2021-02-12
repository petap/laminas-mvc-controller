<?php

namespace Petap\LaminasMvcControllerTest;

use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ViewModelFactory;
use Laminas\View\Model\ViewModel;
use Prophecy\PhpUnit\ProphecyTrait;

class ViewModelFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        /** @var ViewModel $viewModel */
        $viewModel = $this->prophesize(ViewModel::class);

        $viewModel->getTemplate()->willReturn('some-template');

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('viewModel')->willReturn('Some\ViewModel');
        $serviceLocator->get('Some\ViewModel')->willReturn($viewModel->reveal());

        $factory = new ViewModelFactory();

        $resultService = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($viewModel->reveal(), $resultService);
    }

    public function testCreateServiceWithCustomTemplate()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $viewModel = $this->prophesize(ViewModel::class);

        $viewModel->getTemplate()->willReturn(null);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('viewModel')->willReturn('Some\ViewModel');
        $serviceLocator->get('Some\ViewModel')->willReturn($viewModel->reveal());
        $routeMatch->getParam('template')->willReturn('some-template');
        $viewModel->setTemplate('some-template')->willReturn(null);

        $factory = new ViewModelFactory();

        $resultService = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($viewModel->reveal(), $resultService);
    }
}

<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\OptionsFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OptionsFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $options = [
            'allowedMethods' => ['GET', 'POST'],
            'redirectTo' => 'home',
        ];
        $routeMatch->getParam('allowedMethods', [])->willReturn($options['allowedMethods']);
        $routeMatch->getParam('redirectTo')->willReturn($options['redirectTo']);

        $factory = new OptionsFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($options, $service);
    }
}

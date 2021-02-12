<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ChangesValidatorFactory;
use Petap\Controller\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ChangesValidatorFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $changesValidator = $this->prophesize(ValidatorInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('changesValidator')->willReturn('Some\ChangesValidator');
        $serviceLocator->get('Some\ChangesValidator')->willReturn($changesValidator->reveal());

        $factory = new ChangesValidatorFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($changesValidator->reveal(), $service);
    }
}

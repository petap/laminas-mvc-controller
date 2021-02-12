<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\CriteriaValidatorFactory;
use Petap\Controller\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CriteriaValidatorFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $criteriaValidator = $this->prophesize(ValidatorInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('criteriaValidator')->willReturn('Some\CriteriaValidator');
        $serviceLocator->get('Some\CriteriaValidator')->willReturn($criteriaValidator->reveal());

        $factory = new CriteriaValidatorFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($criteriaValidator->reveal(), $service);
    }
}

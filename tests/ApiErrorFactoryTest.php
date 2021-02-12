<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\ApiErrorFactory;
use Petap\LaminasMvcController\ApiError;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ApiErrorFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');

        $serviceLocator->get('Application')
            ->willReturn($app->reveal());

        $app->getMvcEvent()
            ->willReturn($this->prophesize('Laminas\Mvc\MvcEvent')->reveal());

        $factory = new ApiErrorFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(ApiError::class, $service);
    }
}

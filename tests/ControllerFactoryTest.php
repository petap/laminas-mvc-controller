<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Petap\Controller\ValidatorInterface;
use Petap\Controller\RequestInterface;
use Petap\Controller\ResponseInterface;
use Petap\LaminasMvcController\Controller;
use Petap\LaminasMvcController\ControllerFactory;
use Petap\LaminasMvcController\ErrorInterface;
use PetapDomainInterface\ServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ControllerFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);


        $controllerManager = $this->prophesize(ControllerManager::class);
        $controllerManager->getServiceLocator()->willReturn($serviceLocator);

        $serviceLocator->get('petap-laminas-mvc-criteria-validator-factory')
            ->willReturn($this->prophesize(ValidatorInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-changes-validator-factory')
            ->willReturn($this->prophesize(ValidatorInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-service-factory')
            ->willReturn($this->prophesize(ServiceInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-view-model-factory')
            ->willReturn($this->prophesize('Laminas\View\Model\ViewModel')->reveal());

        $serviceLocator->get('petap-laminas-mvc-request-factory')
            ->willReturn($this->prophesize(RequestInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-response-factory')
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-html-error-factory')
            ->willReturn($this->prophesize(ErrorInterface::class)->reveal());

        $serviceLocator->get('petap-laminas-mvc-options-factory')
            ->willReturn([]);

        $factory = new ControllerFactory();

        $controller = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(Controller::class, $controller);
    }
}

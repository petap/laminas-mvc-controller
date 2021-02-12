<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\LaminasMvcController\HtmlErrorFactory;
use Petap\LaminasMvcController\ErrorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HtmlErrorFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $viewModel = $this->prophesize('Laminas\View\Model\ModelInterface');

        $serviceLocator->get('Application')->willReturn($app->reveal());
        $serviceLocator->get('petap-laminas-mvc-view-model-factory')->willReturn($viewModel->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $factory = new HtmlErrorFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(ErrorInterface::class, $service);
    }
}

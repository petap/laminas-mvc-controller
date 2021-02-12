<?php

namespace Petap\LaminasMvcControllerTest;

use Petap\LaminasMvcController\HtmlError;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HtmlErrorTest extends TestCase
{
    use ProphecyTrait;

    private $viewModel;
    private $error;
    private $event;

    public function setUp() : void
    {
        $this->event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $this->viewModel = $this->prophesize('Laminas\View\Model\ViewModel');

        $this->error = new HtmlError(
            $this->event->reveal(),
            $this->viewModel->reveal()
        );
    }

    public function testMethodNotAllowed()
    {
        $zendResponse = $this->prophesize('Laminas\Http\Response');
        $zendResponse->setStatusCode(405)->willReturn(null);

        $this->viewModel->setVariable('message', 'The requested method not allowed')->willReturn(null);
        $this->viewModel->setTemplate('error/404')->willReturn(null);

        $this->event->setResult($this->viewModel->reveal())->willReturn(null);

        $this->event->getResponse()->willReturn($zendResponse->reveal());

        $this->error->methodNotAllowed();
    }

    public function testNotFoundByRequestedCriteria()
    {
        $zendResponse = $this->prophesize('Laminas\Http\Response');
        $zendResponse->setStatusCode(404)->willReturn(null);

        $this->viewModel->setVariable(
            'message',
            'The requested resource was not found by requested criteria'
        )->willReturn(null);
        $this->viewModel->setTemplate('error/404')->willReturn(null);

        $this->event->setResult($this->viewModel->reveal())->willReturn(null);

        $this->event->getResponse()->willReturn($zendResponse->reveal());

        $this->error->notFoundByRequestedCriteria([]);
    }
}

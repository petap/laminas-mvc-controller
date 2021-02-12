<?php

namespace Petap\LaminasMvcControllerTest;

use Petap\Controller\RequestInterface;
use Petap\Controller\ResponseInterface;
use Petap\Controller\Controller as PetapController;
use Petap\LaminasMvcController\Controller;
use Petap\LaminasMvcController\ErrorInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ControllerTest extends TestCase
{
    use ProphecyTrait;

    private $request;
    private $response;
    private $viewModel;
    private $error;
    private $event;

    /**
     * @var Controller
     */
    private $controller;

    public function setUp() : void
    {
        $this->request = $this->prophesize(RequestInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->viewModel = $this->prophesize('Laminas\View\Model\ViewModel');
        $petapController = $this->prophesize(PetapController::class);
        $this->error = $this->prophesize(ErrorInterface::class);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch(new RouteMatch([]));

        $this->controller = new Controller(
            $this->request->reveal(),
            $this->response->reveal(),
            $this->viewModel->reveal(),
            $petapController->reveal(),
            $this->error->reveal()
        );

        $petapController->dispatch($this->request->reveal(), $this->response->reveal())->willReturn(null);
    }

    public function testOnDispatch()
    {
        $data = ['key' => 'value'];
        $this->response->getCriteriaErrors()->willReturn([]);
        $this->response->getChangesErrors()->willReturn([]);
        $this->response->getRedirectTo()->willReturn(null);
        $this->response->toArray()->willReturn($data);
        $this->viewModel->setVariables($data)->willReturn(null);

        $result = $this->controller->onDispatch($this->event);

        $this->assertEquals($this->viewModel->reveal(), $result);
    }

    public function testOnDispatchWithCriteriaError()
    {
        $data = ['key' => 'value'];
        $errorData = ['errorKey' => 'errorValue'];
        $this->response->getCriteriaErrors()->willReturn($errorData);
        $this->error->notFoundByRequestedCriteria($errorData)
            ->willReturn($this->viewModel->reveal());

        $this->response->getChangesErrors()->willReturn([]);
        $this->response->getRedirectTo()->willReturn(null);
        $this->response->toArray()->willReturn($data);
        $this->viewModel->setVariables($data)->willReturn(null);

        $result = $this->controller->onDispatch($this->event);

        $this->assertEquals($this->viewModel->reveal(), $result);
    }

    public function testOnDispatchWithChangesError()
    {
        $data = ['key' => 'value'];
        $errorData = ['errorKey' => 'errorValue'];
        $this->response->getCriteriaErrors()->willReturn([]);
        $this->response->getChangesErrors()->willReturn($errorData);
        $this->response->getRedirectTo()->willReturn(null);
        $this->response->toArray()->willReturn($data);
        $this->viewModel->setVariables($data)->willReturn(null);

        $result = $this->controller->onDispatch($this->event);

        $this->assertEquals($this->viewModel->reveal(), $result);
    }
}

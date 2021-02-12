<?php

namespace Petap\LaminasMvcController;

use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ModelInterface;

class HtmlError implements ErrorInterface
{
    /**
     * @var MvcEvent
     */
    private $mvcEvent;

    /**
     * @var ModelInterface
     */
    private $viewModel;

    /**
     * @param MvcEvent $mvcEvent
     * @param ModelInterface $viewModel
     */
    public function __construct(MvcEvent $mvcEvent, ModelInterface $viewModel)
    {
        $this->mvcEvent = $mvcEvent;
        $this->viewModel = $viewModel;
    }

    /**
     * @return mixed
     */
    public function methodNotAllowed()
    {
        $zendResponse = $this->mvcEvent->getResponse();
        $zendResponse->setStatusCode(405);
        $this->viewModel->setVariable('message', 'The requested method not allowed');
        $this->viewModel->setTemplate('error/404');

        $this->mvcEvent->setResult($this->viewModel);

        return $this->viewModel;
    }

    /**
     * @return mixed
     */
    public function notFoundByRequestedCriteria($criteriaErrors)
    {
        $zendResponse = $this->mvcEvent->getResponse();
        $zendResponse->setStatusCode(404);
        $this->viewModel->setVariable('message', 'The requested resource was not found by requested criteria');
        $this->viewModel->setTemplate('error/404');

        $this->mvcEvent->setResult($this->viewModel);

        return $this->viewModel;
    }

    /**
     * @return mixed
     */
    public function changesErrors($changesErrors) {}
}

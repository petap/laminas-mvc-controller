<?php

namespace Petap\LaminasMvcController;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\View\Model\ModelInterface as ViewModelInterface;
use Laminas\Http\Response;
use Petap\Controller\Controller as PetapController;
use Petap\Controller\RequestInterface;
use Petap\Controller\ResponseInterface;

class Controller extends AbstractController
{
    /**
     * @var RequestInterface
     */
    private $petapRequest;

    /**
     * @var ResponseInterface
     */
    private $petapResponse;

    /**
     * @var ViewModelInterface
     */
    private $viewModel;

    /**
     * @var PetapController
     */
    private $controller;

    /**
     * @var ErrorInterface
     */
    private $error;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param RequestInterface $petapRequest
     * @param ResponseInterface $petapResponse
     * @param ViewModelInterface $viewModel
     * @param PetapController $controller
     * @param ErrorInterface $error
     * @param array $options
     */
    public function __construct(
        RequestInterface $petapRequest,
        ResponseInterface $petapResponse,
        ViewModelInterface $viewModel,
        PetapController $controller,
        ErrorInterface $error,
        array $options = []
    ) {

        $this->petapRequest = $petapRequest;
        $this->petapResponse = $petapResponse;
        $this->viewModel = $viewModel;
        $this->controller = $controller;
        $this->error = $error;
        $this->options = $options;
    }

    /**
     * @param MvcEvent $e
     * @return mixed|\Laminas\Http\Response|ViewModelInterface
     */
    public function onDispatch(MvcEvent $e)
    {
        if (!empty($this->options['allowedMethods'])
            &&  !in_array($this->petapRequest->getMethod(), $this->options['allowedMethods'])) {
            return $this->error->methodNotAllowed();
        }

        $e->setParam('petapRequest', $this->petapRequest);
        $e->setParam('petapResponse', $this->petapResponse);
        $routeName = $e->getRouteMatch()->getMatchedRouteName();

        $this->getEventManager()->trigger("dispatch.$routeName.pre", $e);

        $this->controller->dispatch($this->petapRequest, $this->petapResponse);

        $this->getEventManager()->trigger("dispatch.$routeName.post", $e);

        $criteriaErrors = $this->petapResponse->getCriteriaErrors();
        if (!empty($criteriaErrors)) {
            return $this->error->notFoundByRequestedCriteria($criteriaErrors);
        }

        $changesErrors = $this->petapResponse->getChangesErrors();
        $redirectTo = $this->petapResponse->getRedirectTo();
        if (empty($changesErrors) && !empty($redirectTo)) {
            if (is_array($redirectTo)) {
                if (!isset($redirectTo['route'])) {
                    throw new \RuntimeException('Missing required parameter route');
                }
                $routeParams = isset($redirectTo['params']) ? $redirectTo['params'] : [];
                $routeOptions = isset($redirectTo['options']) ? $redirectTo['options'] : [];

                return $this->redirect()->toRoute($redirectTo['route'], $routeParams, $routeOptions);
            } else {
                return $this->redirect()->toRoute($redirectTo);
            }
        }

        if (!empty($changesErrors)) {
            $result =  $this->error->changesErrors($changesErrors);
            if ($result instanceof Response) {
                return $result;
            }
        }

        $this->viewModel->setVariables($this->petapResponse->toArray());

        $e->setResult($this->viewModel);

        return $this->viewModel;
    }
}

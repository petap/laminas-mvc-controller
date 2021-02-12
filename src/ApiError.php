<?php

namespace Petap\LaminasMvcController;

use Laminas\Mvc\MvcEvent;

class ApiError implements ErrorInterface
{
    /**
     * @var MvcEvent
     */
    private $mvcEvent;

    /**
     * @param MvcEvent $mvcEvent
     */
    public function __construct(MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }

    /**
     * @return mixed
     */
    public function methodNotAllowed()
    {
        $zendResponse = $this->mvcEvent->getResponse();
        $zendResponse->setStatusCode(405);

        return $zendResponse;
    }

    /**
     * @return mixed
     */
    public function notFoundByRequestedCriteria($criteriaErrors)
    {
        $zendResponse = $this->mvcEvent->getResponse();
        $zendResponse->setStatusCode(404);
        $zendResponse->getHeaders()->addHeaderLine("Content-Type", "application/json");
        $zendResponse->setContent(json_encode($criteriaErrors));

        return $zendResponse;
    }

    /**
     * @return mixed
     */
    public function changesErrors($changesErrors) {}
}

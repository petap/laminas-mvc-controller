<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\Controller as PetapController;

class ControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $criteriaValidator = $container->get('petap-laminas-mvc-criteria-validator-factory');
        $changesValidator = $container->get('petap-laminas-mvc-changes-validator-factory');
        $service = $container->get('petap-laminas-mvc-service-factory');

        $viewModel = $container->get('petap-laminas-mvc-view-model-factory');
        $request = $container->get('petap-laminas-mvc-request-factory');
        $response = $container->get('petap-laminas-mvc-response-factory');
        $error = $container->get('petap-laminas-mvc-html-error-factory');
        $options = $container->get('petap-laminas-mvc-options-factory');

        $petapController = new PetapController($criteriaValidator, $changesValidator, $service);

        return new Controller($request, $response, $viewModel, $petapController, $error, $options);
    }
}

<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Petap\Controller\ValidatorInterface;
use Petap\Controller\EmptyValidator;

class CriteriaValidatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        if ($routeMatch->getParam('criteriaValidator')) {
            $criteriaValidator = $container->get($routeMatch->getParam('criteriaValidator'));
            if (! $criteriaValidator instanceof ValidatorInterface) {
                throw new \RuntimeException('Criteria validator must be instance of ' . ValidatorInterface::class);
            }
        } else {
            $criteriaValidator = new EmptyValidator();
        }

        return $criteriaValidator;
    }
}

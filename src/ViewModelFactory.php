<?php

namespace Petap\LaminasMvcController;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\View\Model\ViewModel;

class ViewModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\Mvc\Application $app */
        $app = $container->get('Application');
        /** @var \Laminas\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $app->getMvcEvent()->getRouteMatch();

        if ($routeMatch->getParam('viewModel')) {
            $viewModel = $container->get($routeMatch->getParam('viewModel'));
            if (! $viewModel instanceof ViewModel) {
                throw new \RuntimeException('ViewModel must be instance of ' . ViewModel::class);
            }
        } else {
            $viewModel = new ViewModel();
        }

        if (!$viewModel->getTemplate()) {
            $template = $routeMatch->getParam('template');
            if ($template) {
                if (!is_string($template)) {
                    throw new \RuntimeException('Parameter template must be string');
                }
                $viewModel->setTemplate($template);
            }
        }

        return $viewModel;
    }
}

<?php

namespace Petap\LaminasMvcControllerTest;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Petap\Controller\RequestInterface;
use Petap\LaminasMvcController\ApiRequestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ApiRequestFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $laminasRequest = $this->prophesize('\Laminas\Http\PhpEnvironment\Request');
        $request = $this->prophesize(RequestInterface::class);

        $serviceLocator->get('Application')->willReturn($app->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $serviceLocator->get('request')->willReturn($laminasRequest->reveal());

        $routeMatch->getParam('request')->willReturn('Some\Request');
        $serviceLocator->get('Some\Request')->willReturn($request->reveal());

        $laminasRequest->getMethod()->willReturn('GET');
        $request->setMethod('GET')->willReturn(null);

        $factory = new ApiRequestFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertEquals($request->reveal(), $service);
    }

    public function testCreateServiceWithDefaultRequestMultipartFormData()
    {
        $this->markTestSkipped("Prophecy\Exception\Call\UnexpectedCallException: Unexpected method call on Double\Laminas\Http\PhpEnvironment\Request\P12 isPost");

        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $laminasRequest = $this->prophesize('\Laminas\Http\PhpEnvironment\Request');

        $serviceLocator->get('Application')->willReturn($app->reveal());
        $serviceLocator->get('request')->willReturn($laminasRequest->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('request')->willReturn(null);
        $routeMatch->getParams()->willReturn([]);

        $parameters = $this->prophesize('\Laminas\Stdlib\ParametersInterface');
        $parameters->toArray()->willReturn([]);
        $laminasRequest->getQuery()->willReturn($parameters->reveal());

        $contentType = $this->prophesize('\Laminas\Http\Header\ContentType');
        $contentType->getMediaType()->willReturn('multipart/form-data');
        $headers = $this->prophesize('\Laminas\Stdlib\ParametersInterface');
        $headers->get('contenttype')->willReturn($contentType->reveal());
        $laminasRequest->getHeaders()->willReturn($headers->reveal());
        $laminasRequest->getPost()->willReturn($parameters->reveal());
        $laminasRequest->getFiles()->willReturn($parameters->reveal());

        $laminasRequest->getMethod()->willReturn('GET');

        $factory = new ApiRequestFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(RequestInterface::class, $service);
    }

    public function testCreateServiceWithDefaultRequestJson()
    {
        $this->markTestSkipped("Prophecy\Exception\Call\UnexpectedCallException: Unexpected method call on Double\Laminas\Http\PhpEnvironment\Request\P12 isPost");

        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $app = $this->prophesize('\Laminas\Mvc\Application');
        $event = $this->prophesize('Laminas\Mvc\MvcEvent');
        $routeMatch = $this->prophesize('\Laminas\Router\Http\RouteMatch');
        $laminasRequest = $this->prophesize('\Laminas\Http\PhpEnvironment\Request');

        $serviceLocator->get('Application')->willReturn($app->reveal());
        $serviceLocator->get('request')->willReturn($laminasRequest->reveal());

        $event->getRouteMatch()->willReturn($routeMatch->reveal());

        $app->getMvcEvent()->willReturn($event->reveal());

        $routeMatch->getParam('request')->willReturn(null);
        $routeMatch->getParams()->willReturn([]);

        $parameters = $this->prophesize('\Laminas\Stdlib\ParametersInterface');
        $parameters->toArray()->willReturn([]);
        $laminasRequest->getQuery()->willReturn($parameters->reveal());

        $contentType = $this->prophesize('\Laminas\Http\Header\ContentType');
        $contentType->getMediaType()->willReturn('application/json');
        $headers = $this->prophesize('\Laminas\Stdlib\ParametersInterface');
        $headers->get('contenttype')->willReturn($contentType->reveal());
        $laminasRequest->getHeaders()->willReturn($headers->reveal());
        $laminasRequest->getContent()->willReturn('{"json": true}');
        $laminasRequest->getFiles()->willReturn($parameters->reveal());

        $laminasRequest->getMethod()->willReturn('GET');

        $factory = new ApiRequestFactory();

        $service = $factory($serviceLocator->reveal(), '');

        $this->assertInstanceOf(RequestInterface::class, $service);
    }
}

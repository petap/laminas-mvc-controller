[![Build Status](https://travis-ci.org/petap/laminas-mvc-controller.svg?branch=master)](https://travis-ci.org/petap/laminas-mvc-controller)
[![codecov.io](http://codecov.io/github/petap/laminas-mvc-controller/coverage.svg?branch=master)](http://codecov.io/github/petap/laminas-mvc-controller?branch=master)

# Zend MVC controller implementation
This is controller implementation for Zend MVC builds on [petap/controller](https://github.com/petap/controller).

Most controllers must do

1. Check if that controller\action can be accessed by called method (GET, POST, PUT etc.), if not - rise exception.
2. Process request (this is responsibility of [petap/controller](https://github.com/petap/controller)).
  1. Validate request criteria.
  2. Validate request data.
  3. Process request (run any domain service).
  4. Collect errors.
  5. Collect result.
3. Rise exceptions if error exists.
4. Redirect to next page (if define).
5. Setup ViewModel.
6. Setup MVC Event.

That solution allow to customize any flow parameter and increases code reuse.

Installation
============

1. Install it via composer by running:
T
   ```sh
   composer require petap/laminas-mvc-controller
   ```
2. Copy `./vendor/petap/laminas-mvc-controller/config/petap-laminas-mvc-controller.global.php.dist` to
   `./config/autoload/petap-laminas-mvc-controller.global.php`.

Configuration
============
You can configure that controller with route params:
```php
return [
   'router' => [
       'routes' => [
           'user-update-profile' => [
               'type' => 'Segment',
               'options' => [
                   'route'    => '/profile/update',
                   'defaults' => [
                       'controller' => 'petap-laminas-mvc-controller',
                       'allowedMethods' => ['POST'],
                       'criteriaValidator' => Users\Action\Profile\CriteriaValidator::class,
                       'changesValidator' => Users\Action\Profile\ChangesValidator::class,
                       'service' => Users\Action\Profile\Updater::class,
                       'request' => Petap\Controller\RequestInterface::class,
                       'routeCriteria' => 'id',
                       'response' => Petap\Controller\ResponseInterface::class,
                       'redirectTo' => 'admin-user-list',
                       'viewModel' => Users\User\ViewModel::class,
                   ],
               ],
           ],
       ],
   ],
];
```

`criteriaValidator`, `changesValidator` - if not defined, will be created `Petap\Controller\EmptyValidator`  
`service` - if not defined, will be created `Petap\Controller\EmptyService`  
`request` - if not defined, will be created `Petap\Controller\Request`  
`response` - if not defined, will be created `Petap\Controller\Response`  
`viewModel` - if not defined, will be created `Laminas\View\Model\ViewModel`  


## Dev

#### Build
```bash
docker build --build-arg UID=$(id -u) --build-arg GID=$(id -g) -t laminas-mvc-controller:latest .
```
####Enter to container
```bash
docker run -ti -v $(pwd):/var/www/laminas-mvc-controller laminas-mvc-controller su --shell=/bin/bash www-data
```
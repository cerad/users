<?php
error_reporting(E_ALL);

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Component\HttpKernel\KernelApp;
use Cerad\Component\HttpMessage\Request;

use Cerad\Component\DependencyInjection\Container;

use Cerad\Module\UserModule\UserParameters;
use Cerad\Module\UserModule\UserServices;
use Cerad\Module\UserModule\UserRoutes;

class UserApp extends KernelApp
{
  protected function registerServices(Container $container)
  {
    parent::registerServices($container);

    new UserParameters($container);
    new UserServices  ($container);
    new UserRoutes    ($container);
  }
}

$app = new UserApp();

$request  = new Request($_SERVER);
$response = $app->handle($request);
$response->send();


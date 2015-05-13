<?php

namespace Cerad\Module\UserModule;

use Cerad\Component\HttpKernel\KernelApp;

use Cerad\Component\DependencyInjection\Container;

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
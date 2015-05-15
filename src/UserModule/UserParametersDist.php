<?php

namespace Cerad\Module\UserModule;

use Cerad\Component\DependencyInjection\Container;

class UserParametersDist
{
  public function __construct(Container $container)
  {
    $container->set('db_url_users','mysql://tests:tests@localhost/tests');
  }
}
<?php

namespace Cerad\Module\UserModule;

class UserParameters 
{
  public function __construct($container)
  {
    $container->set('db_url_users','mysql://impd:impd894@localhost/users');
  }
}
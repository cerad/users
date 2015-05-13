<?php

namespace Cerad\Module\AuthModule\Tests;

use Cerad\Component\DependencyInjection\Container;

use Cerad\Module\AuthModule\AuthServices;

class AuthTests extends  \PHPUnit_Framework_TestCase
{
  /** @var  Container $container */
  protected $container;
  
  public static function setUpBeforeClass()
  {
    shell_exec(sprintf('mysql --login-path=tests < %s',__DIR__ . '/../../UserModule/schema.sql'));
    shell_exec(sprintf('mysql --login-path=tests < %s',__DIR__ . '/schema_data.sql'));
  }
  public function setUp()
  {
    $this->container = $container = new Container();
    
    $container->set('secret','someSecret');
    
    $container->set('cerad_user_master_password','testing');
  
    $container->set('db_url_users','mysql://tests:tests@localhost/tests');

    new AuthServices($container);
  }
}
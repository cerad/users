<?php
namespace Cerad\Module\UserModule;

//use Cerad\Module\UserModule\UserApp;

use Cerad\Component\HttpMessage\Request;

use Cerad\Component\DependencyInjection\Container;

class UserRoutesTest extends \PHPUnit_Framework_TestCase
{
  /* @var $app UserApp */
  protected $app;

  /* @var $container Container */
  protected $container;
  
  public static function setUpBeforeClass()
  {
    $schemaFile = __DIR__ . '/schema.sql';
    
    $cmd = 'mysql --login-path=tests < ' . $schemaFile;
    
    shell_exec($cmd);
  }
  public function setUp()
  {
    $this->app = $app = new UserApp();
    
    $this->container = $app->getContainer();
  }
  public function testPost()
  {
    $user = 
    [
      'userName' => 'ahundiak',
      'dispName' => 'Art Hundiak',
      'email'    => 'ahundiak@example.com',
      'roles'    => 'ROLE_USER,ROLE_ADMIN',
    ];
    $userJson = json_encode($user);
    $headers =
    [
      'Content-Type' => 'application/json',
    ];
    $request = new Request('POST /users',$headers,$userJson);
    
    $response = $this->app->handle($request);
    $this->assertEquals(201,$response->getStatusCode());
    
    $userx = $response->getParsedBody();
    $this->assertEquals(1,$userx['id']);
    $this->assertEquals('Active',$userx['status']);
    
  }
  public function testGetOne()
  {
    $request = new Request('GET /users/1');
    
    $response = $this->app->handle($request);
    $this->assertEquals(200,$response->getStatusCode());
    
    $userx = $response->getParsedBody();
    $this->assertEquals('ahundiak',$userx['userName']);
  }
  public function testGetOneFail()
  {
    $request = new Request('GET /users/99');
    
    $response = $this->app->handle($request);
    $this->assertEquals(404,$response->getStatusCode());    
  }
  public function testGetEmail()
  {
    $request = new Request('GET /users?email=ahundiak@example.com');
    
    $response = $this->app->handle($request);
    $this->assertEquals(200,$response->getStatusCode());
    
    $items = $response->getParsedBody();
    $this->assertEquals(1,count($items));
    $item = $items[0];
    $this->assertEquals('Art Hundiak',$item['dispName']);
  }
}
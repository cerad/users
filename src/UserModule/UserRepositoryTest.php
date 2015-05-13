<?php
namespace Cerad\Module\UserModule;

use Cerad\Component\DependencyInjection\Container;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
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
    $app->boot();
    $this->container = $app->getContainer();
  }
  protected function getUserRepo()
  {
    return $this->container->get('user_repository');
  }
  public function testInsertUser()
  {
    $userRepo = $this->container->get('user_repository');
    
    $user = 
    [
      'userName' => 'ahundiak',
      'dispName' => 'Art Hundiak',
      'email'    => 'ahundiak@example.com',
      'roles'    => 'ROLE_USER,ROLE_ADMIN',
    ];
    $userId = $userRepo->insertUser($user);
    
    $this->assertEquals(1,$userId);
    
    return $userId;
  }

  /**
   * @depends testInsertUser
   * @param $userId
   */
  public function testInsertUserAuth($userId)
  {
    $userRepo = $this->container->get('user_repository');
    
    $userAuth = 
    [
      'userId'   => $userId,
      'provider' => 'google',
      'sub'      => '1234123412341234',
      'iss'      => 'oauth.zayso.org',
      'name'     => 'Art Hundiak',
      'email'    => 'ahundiak@example.com',
    ];
    $userAuthId = $userRepo->insertUserAuth($userAuth);
    
    $this->assertEquals(1,$userAuthId);
  }
  public function testInsertUserAndAuths()
  {
    $userRepo = $this->container->get('user_repository');
    
    $user = 
    [
      'userName' => 'ahundiak2',
      'dispName' => 'Art Hundiak',
      'email'    => 'ahundiak2@example.com',
      'roles'    => 'ROLE_USER,ROLE_ADMIN',
    ];
    $auths =
    [ 
      ['provider' => 'google', 'sub' => 'sub1', 'iss' => 'zayso.org'],
      ['provider' => 'google', 'sub' => 'sub2', 'iss' => 'zayso.org'],
      ['provider' => 'google', 'sub' => 'sub3', 'iss' => 'zayso.org'],
    ];
    $user['auths'] = $auths;
    
    $userId = $userRepo->insertUser($user);
    
    $this->assertEquals(2,$userId);
  }
  public function testFindOneUser()
  {
    $userRepo = $this->getUserRepo();
    
    $user = $userRepo->findOne(1);
    
    $this->assertEquals('ahundiak',$user['userName']);
  }
  public function testFindOneUserWithAuths()
  {
    $userRepo = $this->getUserRepo();
    
    $user = $userRepo->findOneWithAuths(2);
    
    $this->assertEquals(3,count($user['auths']));
  }
  public function testFindAll()
  {
    $userRepo = $this->getUserRepo();
    
    $users = $userRepo->findAll();
    
    $this->assertEquals(2,count($users));
    $this->assertEquals('ahundiak2',  $users[1]['userName']);
    $this->assertEquals('Art Hundiak',$users[0]['dispName']);
  }
}
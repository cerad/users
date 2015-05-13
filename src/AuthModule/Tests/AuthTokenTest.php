<?php

namespace Cerad\Module\AuthModule\Tests;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpKernel\Event\KernelRequestEvent;

use Cerad\Module\AuthModule\AuthToken;
use Cerad\Module\AuthModule\AuthTokenListener;
use Cerad\Module\AuthModule\AuthRoleHierarchy;

class AuthTokenTest extends AuthTests
{
  public function testNewToken()
  {
    $token = new AuthToken('ahundiak',['ROLE_USER','ROLE_SRA']);
    
    $this->assertEquals(2, count($token->getRoles()));
  }
  public function testPostToken()
  {
    $jwtCoder   = $this->container->get('jwt_coder');
    $controller = $this->container->get('auth_token_controller');

    $content = json_encode(['userName' => 'ahundiak','password'=>'zzz']);
    $headers = ['Content-Type' => 'application/json'];
    $request = new Request('POST /auth/tokens',$headers,$content);

    /** @var Response $response */
    $response = $controller->postAction($request);
    $this->assertEquals(201, $response->getStatusCode());
    
    $responsePayload = $response->getParsedBody();
    
    $authToken = $responsePayload['authToken'];
    
    $authPayload = $jwtCoder->decode($authToken);
    $this->assertEquals('ahundiak',$authPayload['userName']);
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
   */
  public function testPostTokenUsernameNotFound()
  {
    $controller = $this->container->get('auth_token_controller');

    $content  = json_encode(['userName' => 'ahundiak' . 'x','password'=>'zzz']);
    $headers = ['Content-Type' => 'application/json'];
    $request  = new Request('POST /auth/tokens',$headers,$content);
    $controller->postAction($request);
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
   */
  public function testPostTokenInvalidPassword()
  {
    $controller = $this->container->get('auth_token_controller');

    $content  = json_encode(['userName' => 'ahundiak@testing.com','password'=>'zzz' . 'x']);
    $headers = ['Content-Type' => 'application/json'];
    $request  = new Request('POST /auth/tokens',$headers,$content);
    $controller->postAction($request);
  }
  protected function createRoleHierarchy()
  {
    $hierarchy = 
    [
        'ROLE_USER'     => [],
        'ROLE_ASSIGNOR' => ['ROLE_USER'],
        'ROLE_SRA'      => ['ROLE_ASSIGNOR'],
    ];
    return new AuthRoleHierarchy($hierarchy);
  }
  public function testAuthTokenListener()
  {
    $jwtCoder = $this->container->get('jwt_coder');

    $roleHierarchy = $this->createRoleHierarchy();
    $listener = new AuthTokenListener($roleHierarchy,$jwtCoder);
    $jwt = $jwtCoder->encode(['username' => 'ahundiak@testing.com','roles' => ['ROLE_USER']]);
    
    $headers = ['Authorization' => $jwt];
    $request = new Request('GET /api/referees',$headers);
    $request->setAttribute('_roles','ROLE_USER');
    
    $event = new KernelRequestEvent($request);
    $listener->onKernelRequestAuthToken($event);

    /** @var AuthToken $authToken */
    $authToken = $request->getAttribute('authToken');
    $this->assertEquals('ahundiak@testing.com',$authToken->getUsername());
    
    $listener->onKernelRequestAuthorize($event);
  }
  public function testRoleHierarchy()
  {
    $roleHierarchy = $this->createRoleHierarchy();
    
    $roleHierarchy->getReachableRoles(['ROLE_ASSIGNOR']);
    
    $allowedFalse = $roleHierarchy->isAuthorized(['ROLE_SRA'],['ROLE_ASSIGNOR']);
    $this->assertEquals(false,$allowedFalse);
    
    $allowedTrue = $roleHierarchy->isAuthorized(['ROLE_ASSIGNOR'],'ROLE_SRA');
    $this->assertEquals(true,$allowedTrue);
  }
}
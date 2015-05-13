<?php

namespace Cerad\Module\AuthModule;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cerad\Component\Jwt\JwtCoder;
use Cerad\Component\HttpKernel\Event\KernelRequestEvent;

class AuthTokenListener implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    return 
    [
      KernelRequestEvent::name => 
      [
        ['onKernelRequestAuthToken', 250],
        ['onKernelRequestRoleToken', 250],
        ['onKernelRequestAuthorize', 245]
      ]
    ];
  }
  /** @var  JwtCoder $jwtCoder */
  private $jwtCoder;

  /** @var  AuthRoleHierarchy $roleHierarchy */
  private $roleHierarchy;
  
  public function __construct(AuthRoleHierarchy $roleHierarchy, JwtCoder $jwtCoder)
  {
    $this->jwtCoder      = $jwtCoder;
    $this->roleHierarchy = $roleHierarchy;
  }
  /* =================================================================
   * The firewall
   */
  public function onKernelRequestAuthorize(KernelRequestEvent $event)
  {
    // This is basically the master firewall
    $requestRoles = $event->getRequest()->getAttribute('_roles');
    if (!$requestRoles) return;

    // Need a token
    /** @var AuthToken $authToken */
    $authToken = $event->getRequest()->getAttribute('authToken');
    if (!$authToken)
    {
      throw new AccessDeniedException();
    }
    $userRoles = $authToken->getRoles();

    if ($this->roleHierarchy->isAuthorized($requestRoles,$userRoles)) return;
    
    throw new AccessDeniedException();
  }
  /* =================================================================
   * This pull any auth token from the request header and 
   * tucks it into the request
   */
  public function onKernelRequestAuthToken(KernelRequestEvent $event)
  {
    if (!$event->isMasterRequest()) return;
    
    $jwt = $event->getRequest()->getHeaderLine('Authorization');
    
    if (!$jwt) return;

    $jwtPayload = $this->jwtCoder->decode($jwt);
    
    $authToken = new AuthToken($jwtPayload['username'],$jwtPayload['roles']);
    
    $event->getRequest()->setAttribute('authToken',$authToken);
  }
  public function onKernelRequestRoleToken(KernelRequestEvent $event)
  {
    if (!$event->isMasterRequest()) return;
    
    $params = $event->getRequest()->getQueryParams();
    
    $role = isset($params['role']) ? $params['role'] : null;
    
    if (!$role) return;
      
    $authToken = new AuthToken('role',[strtoupper($role)]);
    
    $event->getRequest()->setAttribute('authToken',$authToken);
  }
}
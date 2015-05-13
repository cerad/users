<?php

/* ================================================================
 * Forked from: Symfony\Component\Security\Core\Role\RoleHierarchy
 * Roles are strings instead of objects
 */
namespace Cerad\Module\AuthModule;

class AuthRoleHierarchy
{
  private   $hierarchy;
  protected $map;

  public function __construct(array $hierarchy)
  {
    $this->hierarchy = $hierarchy;

    $this->buildRoleMap();
  }
  public function isAuthorized($accessRoles,$userRoles)
  {
    if (!is_array($accessRoles)) $accessRoles = [$accessRoles];
    if (!is_array(  $userRoles))   $userRoles =   [$userRoles];
    
    $reachableRoles = $this->getReachableRoles($userRoles);
    foreach($accessRoles as $accessRole)
    {
      if (isset($reachableRoles[$accessRole])) return true;
    }
    return false;
  }
  // Return array indexed by role
  public function getReachableRoles($roles)
  {
    if (!is_array($roles)) $roles = [$roles];
    
    $reachableRoles = [];
    foreach ($roles as $role) 
    {
      $reachableRoles[$role] = $role;
      if (!isset($this->map[$role])) continue;
      
      foreach ($this->map[$role] as $rolex)
      {
        $reachableRoles[$rolex] = $rolex;
      }
    }
    return $reachableRoles;
  }
  protected function buildRoleMap()
  {
    $this->map = array();
    foreach ($this->hierarchy as $main => $roles) 
    {
      $this->map[$main] = $roles;
      $visited = array();
      $additionalRoles = $roles;
      while ($role = array_shift($additionalRoles)) 
      {
        if (!isset($this->hierarchy[$role])) continue;
                
        $visited[] = $role;
        $this->map[$main] = array_unique(array_merge($this->map[$main], $this->hierarchy[$role]));
        $additionalRoles  = array_merge($additionalRoles, array_diff($this->hierarchy[$role], $visited));
      }
    }
  //print_r($this->map);
  }
}

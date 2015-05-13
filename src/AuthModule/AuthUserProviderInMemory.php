<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthUserProviderInMemory implements AuthUserProviderInterface
{
  private $users;
  
  public function __construct($users)
  {
    $this->users = $users;
  }
  public function loadUserByUsername($username)
  {
    if (isset($this->users[$username]))
    {
      $user = 
      [
        'username'    => $username,
        'roles'       => null,
        'email'       => null,
        'password'    => null,
        'salt'        => null,
        'person_name' => null,
        'person_guid' => null,
      ];
      return array_merge($user,$this->users[$username]);
    }
    $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    $ex->setUsername($username);
    throw $ex;
  }
}
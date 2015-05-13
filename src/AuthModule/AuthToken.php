<?php

namespace Cerad\Module\AuthModule;

class AuthToken
{
  protected $roles;
  protected $username;
  
  public function __construct($username,$roles)
  {
    $this->roles = $roles;
    $this->username = $username;
  }
  public function getRoles   () { return $this->roles;    }
  public function getUsername() { return $this->username; }
}


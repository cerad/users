<?php
namespace Cerad\Module\AuthModule;

interface AuthUserProviderInterface
{
  public function loadUserByUsername($username);
}
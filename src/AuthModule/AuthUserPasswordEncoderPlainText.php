<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AuthUserPasswordEncoderPlainText
{
  public function encodePassword($raw)
  {
    return $raw;
  }
  public function isPasswordValid($encoded, $raw)
  {
    if ($encoded == $raw) return true;
    
    throw new BadCredentialsException('Invalid Password');
  }
}
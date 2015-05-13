<?php
namespace Cerad\Module\AuthModule;

use Cerad\Component\Jwt\JwtCoder;
use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\ResponseJson;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class AuthTokenController
{
  /** @var  JwtCoder *jwtCoder */
  private $jwtCoder;

  /** @var  AuthUserProviderInterface $userProvider */
  private $userProvider;

  /** @var  PasswordEncoderInterface $userPasswordEncoder */
  private $userPasswordEncoder;
  
  public function __construct(
    JwtCoder $jwtCoder,
    AuthUserProviderInterface $userProvider,
    PasswordEncoderInterface  $userPasswordEncoder)
  {
    $this->jwtCoder            = $jwtCoder;
    $this->userProvider        = $userProvider;
    $this->userPasswordEncoder = $userPasswordEncoder;
  }
  public function postAction(Request $request)
  {
    $requestPayload = $request->getParsedBody();
    
    $username = $requestPayload['username'];
    $password = $requestPayload['password'];
    
    $user = $this->userProvider->loadUserByUsername($username);
    $salt = isset($user['salt']) ? $user['salt'] : null;
    
    $this->userPasswordEncoder->isPasswordValid($user['password'],$password,$salt);
    
    // Need array_values because index can get messed up
    $roles = is_array($user['roles']) ? array_values($user['roles']) : [$user['roles']];

    $jwtPayload =
    [
      'iat'         => time(),
      'username'    => $username,
      'roles'       => $roles,
      'person_name' => $user['person_name'],
      'person_guid' => $user['person_guid'],
    ];
    $jwt = $this->jwtCoder->encode($jwtPayload);
    
    $jwtPayload['auth_token'] = $jwt;
    
    return new ResponseJson($jwtPayload,201);
  }
}
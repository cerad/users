<?php
namespace Cerad\Module\AuthModule\Tests;

class AuthUserTest extends AuthTests
{
  /* =======================================================
   * DAO Testing
   */
  public function testUserProviderDao()
  {
    $userProvider = $this->container->get('auth_user_provider_dao');
    $user = $userProvider->loadUserByUsername('ahundiak@testing.com'); //print_r($user);
    $this->assertEquals('Art Hundiak',$user['dispName']);
    
    $userPasswordEncoder = $this->container->get('auth_user_password_encoder_dao');
    
    $userPasswordEncoder->isPasswordValid($user['password'],'testing',$user['salt']);
    $userPasswordEncoder->isPasswordValid($user['password'],'zzz',    $user['salt']);
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
   */
  public function testUserProviderDaoUsernameFail()
  {
    $userProvider = $this->container->get('auth_user_provider_dao');
    $userProvider->loadUserByUsername('ahundiak@testing.com' . 'x');
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
   */
  public function testUserProviderDaoPasswordFail()
  {
    $userProvider = $this->container->get('auth_user_provider_dao');
    $user = $userProvider->loadUserByUsername('ahundiak@testing.com');
    
    $userPasswordEncoder = $this->container->get('auth_user_password_encoder_dao');
    $userPasswordEncoder->isPasswordValid($user['password'],'wrong',$user['salt']);
  }
  public function testDefaultRoles()
  {
    $userProvider = $this->container->get('auth_user_provider_dao');
    $user = $userProvider->loadUserByUsername('bailey5000');
    $this->assertEquals('ROLE_USER',$user['roles']);
  }
  /* ===============================================
   * In Memory testing
   */
  public function testUserProviderInMemory()
  {
    $userProvider = $this->container->get('auth_user_provider_in_memory');
    $user = $userProvider->loadUserByUsername('sra');
    $this->assertEquals('sra',$user['username']);
    
    $userPasswordEncoder = $this->container->get('auth_user_password_encoder_plain_text');
    $userPasswordEncoder->isPasswordValid($user['password'],'sra');
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
   */
  public function testUserProviderInMemoryUsernameFail()
  {
    $userProvider = $this->container->get('auth_user_provider_in_memory');
    $userProvider->loadUserByUsername('sra' . 'x');
  }
  /**
   * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
   */
  public function testUserProviderInMemoryPasswordFail()
  {
    $userProvider = $this->container->get('auth_user_provider_in_memory');
    $user = $userProvider->loadUserByUsername('sra');
    
    $userPasswordEncoder = $this->container->get('auth_user_password_encoder_plain_text');
    $userPasswordEncoder->isPasswordValid($user['password'],'wrong');
  }
  /* =======================================
   * Test encoding password
   */
  public function sestPasswordEncoder()
  {
    $userPasswordEncoder = $this->container->get('auth_user_password_encoder_dao');
    
    foreach(['pass1','pass2','pass3'] as $pass)
    {
      $encoded = $userPasswordEncoder->encodePassword($pass,'salt');
      echo sprintf("%s %s\n",$pass,$encoded);
    }
  }
}
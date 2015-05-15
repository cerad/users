<?php
namespace Cerad\Module\UserModule;

use /** @noinspection PhpUnusedAliasInspection */
  Cerad\Module\UserModule\UserRepository;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\ResponseJson;

class UserController
{
  /** @var  UserRepository $userRepository */
  private $userRepository;
  
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }
  public function getOneAction(/** @noinspection PhpUnusedParameterInspection */
    Request $request,$userId)
  {
    $user = $this->userRepository->findOne($userId);
    
    return $user ? new ResponseJson($user,200) : new Response(null,404);
  }
  public function postAction(Request $request)
  {
    $user = $request->getParsedBody();
    
    $userId = $this->userRepository->insertUser($user);
    
    $userx = $this->userRepository->findOne($userId);
    
    return new ResponseJson($userx,201);
  }
  public function searchAction(Request $request)
  {
    $query = $request->getQueryParams();
    
    $items = $this->userRepository->findBy($query);
    
    return new ResponseJson($items,200);
  }
}

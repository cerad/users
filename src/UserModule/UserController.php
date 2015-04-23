<?php
namespace Cerad\Module\UserModule;

use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\ResponseJson;

class UserController
{
  private $userRepository;
  
  public function __construct($userRepository)
  {
    $this->userRepository = $userRepository;
  }
  public function getOneAction($request,$userId)
  {
    $user = $request->getParsedBody();
    
    $userx = $this->userRepository->findOne($userId);
    
    return $userx ? new ResponseJson($userx,200) : new Response(null,404);
  }
  public function postAction($request)
  {
    $user = $request->getParsedBody();
    
    $userId = $this->userRepository->insertUser($user);
    
    $userx = $this->userRepository->findOne($userId);
    
    return new ResponseJson($userx,201);
  }
  public function searchAction($request)
  {
    $query = $request->getQueryParams();
    
    $items = $this->userRepository->findBy($query);
    
    return new ResponseJson($items,200);
  }
}

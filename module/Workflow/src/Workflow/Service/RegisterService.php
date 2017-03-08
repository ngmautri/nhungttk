<?php

namespace User\Service;


use Zend\EventManager\EventManagerInterface;
use Zend\Permissions\Acl\Acl;
use User\Model\User;
use User\Model\UserTable;
use MLA\Service\AbstractService;

 /* 
 * @author nmt
 *
 */
class RegisterService extends AbstractService
{	
	
	/**
	 * @var EventManagerInterface
	 */
	protected $eventManager = null;
	
	protected $userTable;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \MLA\Service\AbtractService::initAcl()
	 */
	public function initAcl(Acl $acl){
		// TODO
	}
	
	
	public function setEventManager(EventManagerInterface $eventManager)
	{
		$eventManager->setIdentifiers(array(__CLASS__));
		$this->eventManager = $eventManager;
	}
	
	public function getEventManager()
	{
		return $this->eventManager;
	}
	
	public function doRegister(User $user)
	{
		
		// Do register
		
		$this->getEventManager()->trigger(
				'postRegister', __CLASS__, array('user' => $user)
				);
		return;
	}
	
	
	public function confirmRegister($key,$email)
	{
		
	}
	
	
	public function setUserTable(UserTable $userTable)	
	{
		$this->userTable =  $userTable;		
	}
	
	public function getUserTable()
	{
		return $this->userTable;
	}
}
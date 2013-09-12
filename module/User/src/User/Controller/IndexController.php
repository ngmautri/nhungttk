<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public $userTable;
    public $authService;
    public $massage = 'NULL';

    public function indexAction()
    {
        if ($this->getAuthService()->hasIdentity()){
            $massage = "loged in";
        }else {
            $massage = "not loged in yet";
        }


        return new ViewModel(array(
            'users'     => $this->getUserTable()->fetchAll(),
            'massage'   => $massage
        ));
    }

    // get UserTable
    public function getUserTable()
    {
    	if (!$this->userTable) {
    		$sm = $this->getServiceLocator();
    		$this->userTable = $sm->get('User\Model\UserTable');
    	}
    	return $this->userTable;
    }


    public function getAuthService(){
        if (! $this->authService) {
        	$this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;
use Zend\Barcode\Barcode;


use User;
use User\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public $userTable;
    public $authService;
    public $massage = 'NULL';

    /*
     * Defaul Action
     */

    public function indexAction()
    {

    	$this->NMTPlugin()->test();
    	$form = new UserForm();

        if ($this->getAuthService()->hasIdentity()){
            $massage = "loged in";
        }else {
            $massage = "not loged in yet";
        }




        return new ViewModel(array(
            'users'     => $this->getUserTable()->fetchAll(),
            'massage'   => $massage,
        	'form'		=> $form
        ));
    }

<<<<<<< .mine

    public function barcodeAction(){
        // Only the text to draw is required


        $barcodeOptions = array('text' => 'ME.0000012');

        // No required options
        $rendererOptions = array();

        // Draw the barcode in a new image,
        Barcode::factory('code39', 'image', $barcodeOptions, $rendererOptions)->render();
    }


=======
    public function testAction(){
    	$this->NMTPlugin()->test();
    	return new ViewModel();
    }


>>>>>>> .r20
    // get UserTable
    public function getUserTable()
    {
    	if (!$this->userTable) {
    		$sm = $this->getServiceLocator();
    		$this->userTable = $sm->get('User\Model\UserTable');
    	}
    	return $this->userTable;
    }


    /*
     * Get Authentication Service
     */
    public function getAuthService(){
        if (! $this->authService) {
        	$this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }
}

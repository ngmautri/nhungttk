<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * 
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SettingController extends AbstractActionController {
	
	public function indexAction() {
		return new ViewModel ();
	}
}

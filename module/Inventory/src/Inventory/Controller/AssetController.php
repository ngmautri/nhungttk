<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Barcode\Barcode;

class AssetController extends AbstractActionController
{
    public $assetTypeTable;
    public $authService;
    public $massage = 'NULL';

    /*
     * Defaul Action
     */

    public function indexAction()
    {

    }
    
    public function addAction(){
    	
    }
    
    public function editAction(){
    	 
    }
    
    public function deleteAction(){
    
    }
    
	public function typesAction(){
		
		return new ViewModel(array(
				'assetTypes'     => $this->getAssetTypeTable()->fetchAll(),
		));
		
    
    }
    
    // get AssetTable
    private function getAssetTypeTable()
    {
    	if (!$this->assetTypeTable) {
    		$sm = $this->getServiceLocator();
    		$this->assetTypeTable = $sm->get('Inventory\Model\AssetTypeTable');
    	}
    	return $this->assetTypeTable;
    }
    
}

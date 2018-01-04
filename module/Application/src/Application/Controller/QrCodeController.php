<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\QrCode;

/**
 *
 * @author nmt
 *        
 */
class QrCodeController extends AbstractActionController {
    
    
    protected $doctrineEM;
    
		
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}

	
    /**
     * 
     */
	public function testAction() {
	     
	    $qrCode = new QrCode('inventory/item/show?token=8_oN_6VBBS_1fwkV71Q8xiqwqY1q4chr&entity_id=3377&checksum=fa0d9289a1a311dae94ad9d6b7117655');
	    $qrCode->setSize(80);
	    header('Content-Type: '.$qrCode->getContentType());
	    echo $qrCode->writeString();
	}
	
    /**
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

	

	
}

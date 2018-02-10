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

/**
 *
 * @author nmt
 *        
 */
class BackupController extends AbstractActionController {
    
    const BACKUP_FOLDER = "/data/back-up/db";
    
    
    protected $doctrineEM;
    
		
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}

	
    /**
     * 
     */
	public function dbAction() {
	    //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
	    //exec('mysqldump --user=... --password=... --host=... DB_NAME > /path/to/output/file.sql');
	    
	    $fileName = ROOT.self::BACKUP_FOLDER.'/sql_' . date ("m-d-Y");
	    echo $fileName;
	    
	    //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
	    exec('mysqldump -u root --password=kflg79 mla --result-file=' . $fileName . '.sql');
	    
	    //AbtractController is EventManagerAware.
	    $this->getEventManager()->trigger('system.log', __CLASS__, array(
	        'priority' => 7,
	        'message' => 'Database backed up'
	    ));
	    
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

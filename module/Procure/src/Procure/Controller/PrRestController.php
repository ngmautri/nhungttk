<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Doctrine\ORM\EntityManager;



/**
 *
 * @author nmt
 *        
 */
class PrRestController extends AbstractRestfulController
{

    protected $doctrineEM;
    
   /**
    * 
    * {@inheritDoc}
    * @see \Zend\Mvc\Controller\AbstractRestfulController::get()
    */
    public function get($id)
    {
     
        $a_json_final = array();
        $a_json_final['data'] =  __METHOD__.' get current data with id =  '.$id;
                
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
         
        return $response;
    }
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::getList()
     */
    public function getList()
    {
        $response = $this->getResponseWithHeader()
        ->setContent( __METHOD__.' get the list of data');
        return $response;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::create()
     */
    public function create($data)
    {
        $response = $this->getResponseWithHeader()
        ->setContent( __METHOD__.' create new item of data :
                                                    <b>'.$data['name'].'</b>');
        return $response;
    }
    
    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()
        ->setContent(__METHOD__.' update current data with id =  '.$id.
            ' with data of name is '.$data['name']) ;
        return $response;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::delete()
     */
    public function delete($id)
    {
        $response = $this->getResponseWithHeader()
        ->setContent(__METHOD__.' delete current data with id =  '.$id) ;
        return $response;
    }
    
    /**
     * configure response
     */
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
        //make can accessed by *
        ->addHeaderLine('Access-Control-Allow-Origin','*')
        //set allow methods
        ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');
        
        return $response;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

}

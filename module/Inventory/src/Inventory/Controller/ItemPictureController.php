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
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;

class ItemPictureController extends AbstractActionController
{

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    public function getAction()
    {
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
        if ($entity !== null) {
            
            $pic = new \Application\Entity\NmtInventoryItemPicture();
            $pic = $entity;
            $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . $pic->getFileName();
            
            /** Important! for UBUNTU */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
                //->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        } else {
            return;
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function thumbnail200Action()
    {
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            $pic = new \Application\Entity\NmtInventoryItemPicture();
            $pic = $entity;
            $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            
            /** Important! for UBUNTU */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
            
                /** Important! can cause for UBUNTU */
                //->addHeaderLine('Content-Length', mb_strlen($imageContent)); // can cause problem 
            return $response;
        } else {
            return;
        }
    }
    
    
    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function thumbnail1Action()
    {
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $criteria = array(
            'id' => $entity_id,
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            $pic = new \Application\Entity\NmtInventoryItemPicture();
            $pic = $entity;
            $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            
            /** Important! for UBUNTU */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            return $pic_folder;
        } else {
            return;
        }
    }

    /**
     * Return attachment of a target
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
      /*   if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ; */
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        
        if ($target !== null) {
            
            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'item' => $target_id
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
            /*
             * $this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
             * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
             * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
             * $this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
             */
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $request = $this->getRequest();
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        
        if ($target !== null) {
            
            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'item' => $target_id
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
            /*
             * $this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
             * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
             * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
             * $this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
             */
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /** @var \Application\Entity\NmtInventoryItemPicture $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $visibility = $request->getPost ( 'visibility' );
                $isActive = $request->getPost ( 'isActive' );
                $isDefault = $request->getPost ( 'isDefault' );
                $visibility = $request->getPost ( 'visibility' );
                $remarks = $request->getPost ( 'remarks' );
                $markedForDeletion = $request->getPost ( 'markedForDeletion' );
                
                $entity->setIsActive($isActive);
                $entity->setIsDefault($isDefault);
                $entity->setVisibility($visibility);
                $entity->setRemarks($remarks);
                $entity->setMarkedForDeletion($markedForDeletion);
                
                $this->doctrineEM->persist ( $entity);
                $this->doctrineEM->flush ();
                
                 $this->flashMessenger()->addMessage('Picture "' . $entity->getId() . '" has been updated!');
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /** @var \Application\Entity\NmtInventoryItemPicture $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
        
        if (! $entity == null) {
            
            $target = $entity->getItem();
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}




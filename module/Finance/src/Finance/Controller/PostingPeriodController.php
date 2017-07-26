<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Finance\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtFinPostingPeriod;

/**
 *
 * @author nmt
 *        
 */
class PostingPeriodController extends AbstractActionController
{
    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $periodName = $request->getPost('periodName');
            $periodCode = $request->getPost('periodCode');
            $postingFromDate = $request->getPost('postingFromDate');
            $postingToDate = $request->getPost('postingToDate');
            
            if ($periodName == null) {
                $errors[] = 'Please enter Period Name!';
            }
            
            if ($periodCode == null) {
                $errors[] = 'Please enter Period Code!';
            }
            
            $entity = new NmtFinPostingPeriod();
            $entity->setPeriodName($periodName);
            $entity->setPeriodCode($periodCode);
            $entity->setPeriodStatus("N");
            
            $validator = new Date();
            
            if (! $validator->isValid($postingFromDate)) {
                $errors[] = 'Start Date is not correct or empty!';
            } else {
                $entity->setPostingFromDate(new \DateTime($postingFromDate));
            }
            if (! $validator->isValid($postingToDate)) {
                $errors[] = 'End Date is not correct or empty!';
            } else {
                $entity->setPostingToDate(new \DateTime($postingToDate));
            }
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $this->flashMessenger()->addMessage('Posting Period "' . $periodName . '" is created successfully!');
            
            // $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => null
        ));
    }

    public function closeAction()
    {}

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $criteria = array();
        
        $sort_criteria = array();
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }
    
    
    /**
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();
        
     
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
     
        return new ViewModel(array(
            'list' => $list,
        ));
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

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
use Zend\View\Model\ViewModel;
use Application\Model\AclRoleTable;
use Application\Service\DepartmentService;
use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use MLA\Paginator;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationUom;
use Application\Entity\NmtApplicationDocNumber;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class DocNumberController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
         
            $subjectClass = $request->getPost('subjectClass');
            $docNumberName = $request->getPost('docNumberName');
            $docNumberCode = $request->getPost('docNumberCode');
            $fromNumber = $request->getPost('fromNumber');
            $toNumber = $request->getPost('toNumber');
            $isActive = (int) $request->getPost('isActive');
            $prefix = $request->getPost('prefix');
            $suffix = $request->getPost('suffix');
            $remarks = $request->getPost('remarks');
            
            $entity = new NmtApplicationDocNumber();
            
            $entity->setSubjectClass($subjectClass);
            
            $entity->setCompanyId(1);
            
            if ($docNumberName == "") {
                $errors[] = 'Pls give document range name!';
            } else {
                $entity->setDocNumberName($docNumberName);
            }
            
            $entity->setDocNumberCode($docNumberCode);
            
            $n_validated = 0;
            if (! is_numeric($fromNumber)) {
                $errors[] = 'It must be a number.';
            } else {
                if ($fromNumber <= 0) {
                    $errors[] = 'It must be greater than 0!';
                }
                $entity->setFromNumber($fromNumber);
                $n_validated ++;
            }
            
            if (! is_numeric($toNumber)) {
                $errors[] = 'It must be a number.';
            } else {
                if ($toNumber <= 0) {
                    $errors[] = 'It must be greater than 0!';
                }
                $entity->setToNumber($toNumber);
                $n_validated ++;
            }
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity->setIsActive($isActive);
            $entity->setPrefix($prefix);
            $entity->setSuffix($suffix);
            $entity->setRemarks($remarks);
            
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
            
            $this->flashMessenger()->addMessage($docNumberName . '" is created successfully!');
            
            $redirectUrl = "/application/doc-number/list";
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        $entity = null;
        return new ViewModel(array(
            'errors' => null,
            'entity' => $entity
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /** @var \Application\Entity\NmtApplicationDocNumber $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
            
            if ($entity == null) {
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $subjectClass = $request->getPost('subjectClass');
                $docNumberName = $request->getPost('docNumberName');
                $docNumberCode = $request->getPost('docNumberCode');
                $fromNumber = $request->getPost('fromNumber');
                $toNumber = $request->getPost('toNumber');
                $isActive = (int) $request->getPost('isActive');
                $prefix = $request->getPost('prefix');
                $suffix = $request->getPost('suffix');
                $remarks = $request->getPost('remarks');
                
                if ($docNumberName == "") {
                    $errors[] = 'Pls give document range name!';
                } else {
                    $entity->setDocNumberCode($docNumberName);
                }
                
                $entity->setDocNumberName($docNumberName);
                
                $entity->setSubjectClass($subjectClass);
                
                  
                $n_validated = 0;
                if (! is_numeric($fromNumber)) {
                    $errors[] = 'It must be a number.';
                } else {
                    if ($fromNumber <= 0) {
                        $errors[] = 'It must be greater than 0!';
                    }
                    $entity->setFromNumber($fromNumber);
                    $n_validated ++;
                }
                
                if (! is_numeric($toNumber)) {
                    $errors[] = 'It must be a number.';
                } else {
                    if ($toNumber <= 0) {
                        $errors[] = 'It must be greater than 0!';
                    }
                    $entity->setToNumber($toNumber);
                    $n_validated ++;
                }
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                $entity->setIsActive($isActive);
                $entity->setPrefix($prefix);
                $entity->setSuffix($suffix);
                $entity->setRemarks($remarks);
                
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
                
                $this->flashMessenger()->addMessage($docNumberName . '" is created successfully!');
                
                $redirectUrl = "/application/doc-number/list";
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        
        );
        
        /** @var \Application\Entity\NmtApplicationDocNumber $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
        return new ViewModel(array(
            'errors' => null,
            'entity' => $entity,
            'redirectUrl' => $redirectUrl
        
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        
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
        
        $is_active = (int) $this->params()->fromQuery('is_active');
        
        if ($is_active == null) {
            $is_active = 1;
        }
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
	    endif;
        
        if ($sort == null) :
            $sort = "DESC";
	    endif;
        
        $criteria = array();
        $sort_criteria = array();
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findBy($criteria, $sort_criteria);
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState
        
        ));
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

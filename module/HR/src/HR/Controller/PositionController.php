<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtHrEmployee;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtHrPosition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PositionController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Add new contract for employee
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $errors = array();
            // $redirectUrl = $request->getPost('redirectUrl');
            $positionCode = $request->getPost('positionCode');
            
            $positionName = $request->getPost('positionName');
            $costCenter = $request->getPost('costCenter');
            $description = $request->getPost('description');
            $isActive = (int) $request->getPost('isActive');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            if ($positionName == null) {
                $errors[] = 'Please enter Postion Name!';
            }
            
            $entity = new NmtHrPosition();
            $entity->setPositionCode($positionCode);
            $entity->setPositionName($positionName);
            $entity->setCostCenter($costCenter);
            $entity->setIsActive($isActive);
            $entity->setDescription($description);
            if (count($errors) > 0) {
                
                return new ViewModel(array(
                    'redirectUrl' => null,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            
            $redirectUrl = "/hr/position/list";
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $this->flashMessenger()->addMessage("Postion: '" . $entity->getPositionName() . "' has been created!");
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        $redirectUrl = null;
        $entity = new NmtHrPosition();
        $entity->setIsActive(1);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrPosition $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findOneBy($criteria);
        
        if (! $entity == null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }
    
    /**
     * Edit Postion
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
            
            /**@var \Application\Entity\NmtHrPosition $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findOneBy($criteria);
            
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
                
                $oldEntity =  clone($entity);
                
                $errors = array();
                // $redirectUrl = $request->getPost('redirectUrl');
                $positionCode = $request->getPost('positionCode');
                
                $positionName = $request->getPost('positionName');
                $costCenter = $request->getPost('costCenter');
                $description = $request->getPost('description');
                $isActive = (int) $request->getPost('isActive');
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                if ($positionName == null) {
                    $errors[] = 'Please enter Postion Name!';
                }
                
                $entity->setPositionCode($positionCode);
                $entity->setPositionName($positionName);
                $entity->setCostCenter($costCenter);
                $entity->setIsActive($isActive);
                $entity->setDescription($description);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => null,
                        'errors' => $errors,
                        'entity' => $entity
                    ));
                }
                
                // NO ERROR
                
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                
                $changeOn =new \DateTime();
                
               
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                // trigger uploadPicture. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('hr.change.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => 'Postion ' . $entity->getPositionName() .' changed!',
                    'objectId'=> $entity->getId(),
                    'objectToken'=> $entity->getToken(),
                    'changeArray' =>$changeArray,
                    'changeBy' =>$u,
                    'changeOn' =>$changeOn,
                ));
                
                //$entity->setLastchangeBy($u);
                //$entity->setLastchangeOn($changeOn);
                
                $redirectUrl = "/hr/position/list";
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $this->flashMessenger()->addMessage("Position '" . $entity->getPositionName() . "' changed!");
                return $this->redirect()->toUrl($redirectUrl);
                
            }
        }
        
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrPosition $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findOneBy($criteria);
        
        if (! $entity == null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * List Postion of system
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $criteria = array(
            'isActive'=>1            
        );
        
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
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     * list contract for employee
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $employeeCode = $request->getPost('employeeCode');
            $employeeName = $request->getPost('employeeName');
            $employeeNameLocal = $request->getPost('employeeNameLocal');
            $birthday = $request->getPost('birthday');
            $gender = $request->getPost('gender');
            $remarks = $request->getPost('remarks');
            
            $entity = new NmtHrEmployee();
            
            if ($employeeCode == null) {
                $errors[] = 'Please enter employee code!';
            }
            
            if ($employeeName == null) {
                $errors[] = 'Please enter employee name!';
            }
            
            if ($gender == null) {
                $errors[] = 'Please select gender!';
            }
            $validator = new Date();
            if (! $validator->isValid($birthday)) {
                $errors[] = 'Birthday is not correct or empty!';
            } else {
                $entity->setBirthday(new \DateTime($birthday));
            }
            
            // change target
            $criteria = array(
                "employeeCode" => $employeeCode
            );
            $ck = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findby($criteria);
            
            if (count($ck) > 0) {
                $errors[] = 'Employee Code: "' . $employeeCode . '"  exits already';
            } else {
                $entity->setEmployeeCode($employeeCode);
            }
            
            $entity->setEmployeeName($employeeName);
            $entity->setEmployeeNameLocal($employeeNameLocal);
            $entity->setGender($gender);
            $entity->setRemarks($remarks);
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            
            if (count($errors) > 0) {
                
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            $entity->setChecksum(md5(uniqid("employee_" . $entity->getId()) . microtime()));
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            // $new_entity_id = $entity->getId();
            
            /**
             *
             * @todo : update index
             */
            $this->employeeSearchService->addDocument($entity, false);
            $this->flashMessenger()->addMessage("Employee '" . $employeeCode . "' has been created!");
            return $this->redirect()->toUrl($redirectUrl);
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

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM($doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}

<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtHrEmployee;
use Zend\Validator\Date;
use Zend\Math\Rand;
use HR\Service\EmployeeSearchService;
use Application\Entity\NmtHrContract;

/**
 *
 * @author nmt
 *        
 */
class EmployeeContractController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $employeeSearchService;

    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * show contract of an employee
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrContract $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
        
        if ($entity instanceof \Application\Entity\NmtHrContract) {
            
            $criteria = array(
                'isActive' => 1
            );
            $sort_criteria = array(
                'currency' => 'ASC'
            );
            
            $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
            
            $criteria = array(
                'isActive' => 1
            );
            $sort_criteria = array(
                'positionName' => 'ASC'
            );
            
            $position_list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getEmployee(),
                'currency_list' => $currency_list,
                'position_list' => $position_list
            
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * List contract of an employee
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        return new ViewModel();
    }

    /**
     * List contract of an employee
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrEmployee $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtHrEmployee) {
            $criteria = array(
                'employee' => $target_id
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
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
     * Show current contract of an employee
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function getActiveContractAction()
    {
        return new ViewModel();
    }

    /**
     * Add new contract for employee
     * An intinial contract has revision no = 0
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $target_id = $request->getPost('target_id');
            $token = $request->getPost('target_token');
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
            
            if (! $target instanceof NmtHrEmployee) {
                
                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
            } else {
                
                $minimum_wage = 900000;
                $contractNumber = $request->getPost('contractNumber');
                $contractDate = $request->getPost('contractDate');
                $effectiveFrom = $request->getPost('effectiveFrom');
                $effectiveTo = $request->getPost('effectiveTo');
                
                $workingTimeFrom = $request->getPost('workingTimeFrom');
                $workingTimeTo = $request->getPost('workingTimeTo');
                
                $basicSalary = $request->getPost('basicSalary');
                
                $position_id = (int) $request->getPost('position_id');
                $currency_id = (int) $request->getPost('currency_id');
                $isActive = (int) $request->getPost('isActive');
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                $entity = new NmtHrContract();
                $entity->setEmployee($target);
                
                if ($contractNumber == null) {
                    $errors[] = 'Please enter Contract Number!';
                } else {
                    $entity->setContractNumber($contractNumber);
                }
                
                $validator = new Date();
                
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Employement Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
                
                $validated = 0;
                if (! $validator->isValid($effectiveFrom)) {
                    $errors[] = 'Contract Start Date is not correct or empty!';
                } else {
                    $entity->setEffectiveFrom(new \DateTime($effectiveFrom));
                    $validated ++;
                }
                
                // accept empty
                if ($effectiveTo != null) {
                    if (! $validator->isValid($effectiveTo)) {
                        $errors[] = 'Contract End Date is not correct!';
                    } else {
                        $entity->setEffectiveTo(new \DateTime($effectiveTo));
                        $validated ++;
                    }
                }
                
                if ($validated == 2) {
                    if (new \DateTime($effectiveTo) < new \DateTime($effectiveFrom)) {
                        $errors[] = 'Contract End Date must be in the futuret!';
                    }
                }
                
                if ($position instanceof \Application\Entity\NmtHrPosition) {
                    $entity->setPosition($position);
                } else {
                    $errors[] = 'Position can\'t be empty. Please select a postiton!';
                }
                
                if (! is_numeric($basicSalary)) {
                    $errors[] = 'BasicSalary is not valid. It must be a number.';
                } else {
                    if ($basicSalary <= 0) {
                        $errors[] = 'BasicSalary must be greate than 0!';
                    } elseif ($basicSalary < $minimum_wage) {
                        $errors[] = 'BasicSalary is less than minumum wage!';
                    }
                    $entity->setBasicSalary($basicSalary);
                }
                
                $currency = null;
                if ($currency_id > 0) {
                    /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                    $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
                }
                
                $position = null;
                if ($position_id > 0) {
                    /** @var \Application\Entity\NmtHrPosition  $position ; */
                    $position = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->find($currency_id);
                }
                
                if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                    $entity->setCurrency($currency);
                } else {
                    $errors[] = 'Currency can\'t be empty. Please select a currency!';
                }
                
                if ($position instanceof \Application\Entity\NmtHrPosition) {
                    $entity->setPosition($position);
                } else {
                    $errors[] = 'Position can\'t be empty. Please select a postion!';
                }
                
                $entity->setWorkingTimeFrom($workingTimeFrom);
                $entity->setWorkingTimeTo($workingTimeTo);
                
                if (count($errors) > 0) {
                    
                    $criteria = array(
                        'isActive' => 1
                    );
                    $sort_criteria = array(
                        'currency' => 'ASC'
                    );
                    
                    $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
                    
                    $criteria = array(
                        'isActive' => 1
                    );
                    $sort_criteria = array(
                        'positionName' => 'ASC'
                    );
                    
                    $position_list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
                    
                    return new ViewModel(array(
                        'redirectUrl' => null,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity,
                        'currency_list' => $currency_list,
                        'position_list' => $position_list
                    
                    ));
                }
                
                // NO ERROR
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                
                // $redirectUrl = "/hr/position/list";
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $this->flashMessenger()->addMessage("Contract: '" . $entity->getContractNumber() . "' has been created!");
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'positionName' => 'ASC'
        );
        
        $position_list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
        
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $request->getHeader('Referer');
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrEmployee $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtHrEmployee) {
            
            $entity = new NmtHrContract();
            $entity->setIsActive(1);
            $entity->setRevisionNo(0);
            $entity->setWorkingTimeFrom(8);
            $entity->setWorkingTimeTo(17);
            
            // generate document
            $criteria = array(
                'id' => 2
            );
            $default_cur = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findOneBy($criteria);
            $entity->setCurrency($default_cur);
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $target,
                'currency_list' => $currency_list,
                'position_list' => $position_list
            
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * Amendment of a contract
     * This works as following
     * <ul>
     * <li>- clone the last revised active contract </li>
     * <li>- do amendment on this cloned object </li>
     * <li>- save the new contract with updated revision number </li>
     * </ul>
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function amendAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtHrContract $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
            $errors = array();
            
            if (! $entity instanceof \Application\Entity\NmtHrContract) {
                
                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $oldEntity = clone ($entity);
                
                $minimum_wage = 900000;
                
                $changeDate = $request->getPost('changeDate');
                $changeValidFrom = $request->getPost('changeValidFrom');
                $contractStatus = $request->getPost('contractStatus');
                
                
                $contractNumber = $request->getPost('contractNumber');
                $contractDate = $request->getPost('contractDate');
                $effectiveFrom = $request->getPost('effectiveFrom');
                $effectiveTo = $request->getPost('effectiveTo');
                
                $workingTimeFrom = $request->getPost('workingTimeFrom');
                $workingTimeTo = $request->getPost('workingTimeTo');
                
                $basicSalary = $request->getPost('basicSalary');
                
                $position_id = (int) $request->getPost('position_id');
                $currency_id = (int) $request->getPost('currency_id');
                $isActive = (int) $request->getPost('isActive');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                if ($contractNumber == null) {
                    $errors[] = 'Please enter Contract Number!';
                } else {
                    $entity->setContractNumber($contractNumber);
                }
                
                $entity->setContractStatus($contractStatus);
                $entity->setIsActive($isActive);
                
                $validator = new Date();
                
                $changeOn = new \DateTime();
                
                
                if ($changeDate != null) {
                    if (! $validator->isValid($changeDate)) {
                        $errors[] = 'Change Date is not correct or empty!';
                     }else{
                         $newChangeDate =  new \DateTime($changeDate);
                         
                    }
                }else{
                    $newChangeDate = $changeOn;
                }
                
                if ($changeValidFrom != null) {
                    if (! $validator->isValid($changeValidFrom)) {
                        $errors[] = 'Change Valid From is not correct or empty!';
                    }else{
                        $newChangeValidFrom =  new \DateTime($changeValidFrom);
                    }
                }else{
                    $newChangeValidFrom =$changeOn;
                }
                
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Employement Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
                
                $validated = 0;
                if (! $validator->isValid($effectiveFrom)) {
                    $errors[] = 'Contract Start Date is not correct or empty!';
                } else {
                    $entity->setEffectiveFrom(new \DateTime($effectiveFrom));
                    $validated ++;
                }
                
                // accept empty
                if ($effectiveTo != null) {
                    if (! $validator->isValid($effectiveTo)) {
                        $errors[] = 'Contract End Date is not correct!';
                    } else {
                        $entity->setEffectiveTo(new \DateTime($effectiveTo));
                        $validated ++;
                    }
                }else{
                    $entity->setEffectiveTo(NULL);
                }
                
                if ($validated == 2) {
                    if (new \DateTime($effectiveTo) < new \DateTime($effectiveFrom)) {
                        $errors[] = 'Contract End Date must be in the future!';
                    }
                }
                
                if (! is_numeric($basicSalary)) {
                    $errors[] = 'BasicSalary is not valid. It must be a number.';
                } else {
                    if ($basicSalary <= 0) {
                        $errors[] = 'BasicSalary must be greate than 0!';
                    } elseif ($basicSalary < $minimum_wage) {
                        $errors[] = 'BasicSalary is less than minumum wage!';
                    }
                    $entity->setBasicSalary($basicSalary);
                }
                
                $currency = null;
                if ($currency_id != $oldEntity->getCurrency()->getId()) {
                    /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                    $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
                    if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                        $entity->setCurrency($currency);
                    }
                }
                
                $position = null;
                if ($position_id != $oldEntity->getPosition()->getId()) {
                    /** @var \Application\Entity\NmtHrPosition  $position ; */
                    $position = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->find($position_id);
                    if ($position instanceof \Application\Entity\NmtHrPosition) {
                        $entity->setPosition($position);
                    }
                }
                
                $entity->setWorkingTimeFrom($workingTimeFrom);
                $entity->setWorkingTimeTo($workingTimeTo);
                
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                if(count($changeArray)==0){
                    $errors[] = 'Nothing changed!';                    
                }
                
                if (count($errors) > 0) {
                    
                    $criteria = array(
                        'isActive' => 1
                    );
                    $sort_criteria = array(
                        'currency' => 'ASC'
                    );
                    
                    $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
                    
                    $criteria = array(
                        'isActive' => 1
                    );
                    $sort_criteria = array(
                        'positionName' => 'ASC'
                    );
                    
                    $position_list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'target' => $entity->getEmployee(),
                        'currency_list' => $currency_list,
                        'position_list' => $position_list
                    ));
                }
                
                // NO ERROR
                // +++++++++++++++++++++++++++++++++
                
               
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                
                // trigger log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('hr.contract.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => 'Contract ' . $entity->getContractNumber() . ' changed!',
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => $entity->getRevisionNo(),
                    'changeDate' => $newChangeDate,
                    'changeValidFrom' => $newChangeValidFrom,
                ));
                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $this->flashMessenger()->addMessage("Contract '" . $entity->getContractNumber() . "' changed!");
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        // ++++++++++++++++++++++
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'positionName' => 'ASC'
        );
        
        $position_list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPosition')->findBy($criteria, $sort_criteria);
        
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrContract $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
        
        if ($entity instanceof \Application\Entity\NmtHrContract) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getEmployee(),
                'currency_list' => $currency_list,
                'position_list' => $position_list
            
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Terminate contract
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function terminateAction()
    {
        return new ViewModel();
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
     * @return mixed
     */
    public function getEmployeeSearchService()
    {
        return $this->employeeSearchService;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @param mixed $employeeSearchService
     */
    public function setEmployeeSearchService(EmployeeSearchService $employeeSearchService)
    {
        $this->employeeSearchService = $employeeSearchService;
    }
}

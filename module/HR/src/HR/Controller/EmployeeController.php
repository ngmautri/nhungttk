<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Entity\NmtHrEmployee;
use HR\Service\EmployeeSearchService;
use Application\Domain\Util\Pagination\Paginator;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 *
 * @author nmt
 *        
 */
class EmployeeController extends AbstractGenericController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $employeeSearchService;

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
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
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();

        // var_dump($criteria);

        $sort_criteria = array();

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("employee_" . $entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        /**
         *
         * @todo : update index
         */
        $this->employeeSearchService->createEmployeeIndex();

        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
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
    public function listAction()
    {
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) :
            $is_active = 1;
        endif;

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $criteria = array();

        // var_dump($criteria);

        $sort_criteria = array();

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria, 0, 0);
        }

        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'is_active' => $is_active
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function applicantListAction()
    {
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) :
            $is_active = 1;
        endif;

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $criteria = array();

        // var_dump($criteria);

        $sort_criteria = array();

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findBy($criteria, $sort_criteria, 0, 0);
        }

        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'is_active' => $is_active
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);

        if ($entity !== null) {

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtHrEmployee $entity ;*/

            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
            $oldEntity = clone ($entity);

            if ($entity == null) {

                $errors[] = 'Employee not found or token key is not valid. Please try again!';

                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            } else {
                $employeeCode = $request->getPost('employeeCode');
                $employeeName = $request->getPost('employeeName');
                $employeeNameLocal = $request->getPost('employeeNameLocal');
                $birthday = $request->getPost('birthday');
                $gender = $request->getPost('gender');
                $remarks = $request->getPost('remarks');

                // $entity = new NmtHrEmployee (); // NEED COMMENTED

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

                $entity->setEmployeeCode($employeeCode);
                $entity->setEmployeeName($employeeName);
                $entity->setEmployeeNameLocal($employeeNameLocal);
                $entity->setGender($gender);
                $entity->setRemarks($remarks);

                if (count($errors) > 0) {

                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity
                    ));
                }

                // NO ERROR Do Not change Checksum

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                // $changeArray=$this->objectsAreIdentical($oldEntity, $oldEntity);

                $changeOn = new \DateTime();

                // trigger uploadPicture. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('hr.change.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => 'Employee #' . $entity->getId() . ' has been edited!',
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn
                ));

                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn($changeOn);
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                // $new_entity_id = $entity->getId();

                /**
                 *
                 * @todo : need to update search index
                 */

                $this->flashMessenger()->addMessage("Employee " . $employeeCode . " has been updated! . No of change.:" . count($changeArray));
                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);

        if ($entity !== null) {
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
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function selectAction()
    {
        $request = $this->getRequest();
        $context = $this->params()->fromQuery('context');

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findAll();
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null,
            'context' => $context
        ));
    }

    // SETTER AND GETTER
    public function getEmployeeSearchService()
    {
        return $this->employeeSearchService;
    }

    public function setEmployeeSearchService(EmployeeSearchService $employeeSearchService)
    {
        $this->employeeSearchService = $employeeSearchService;
        return $this;
    }
}

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

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ContractController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

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
     * Edit contract for employee
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
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

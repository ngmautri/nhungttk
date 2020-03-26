<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtHrSalary;
use Doctrine\ORM\EntityManager;
use HR\Payroll\Employee;
use HR\Payroll\Payroll;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Income\Factory\AbstractIncomeFactoryRegistry;
use HR\Payroll\Input\ConsolidatedPayrollInput;
use Zend\Math\Rand;
use Exception;
use HR\Payroll\Income\Factory\AbstractIncomeFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SalaryController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

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
     * Add Salary component for an contract revision
     *
     * @param
     *            : contract revision
     *            
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        return new ViewModel();
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function assignAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $target_id = (int) $request->getPost('target_id');
            $token = $request->getPost('token');
            $incomes = $request->getPost('incomes');
            $redirectUrl = $request->getPost('redirectUrl');

            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtHrContract $target ; */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
            $errors = array();

            if (! $target instanceof \Application\Entity\NmtHrContract) {

                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors
                ));

                // might need redirect
            } else {
                if (count($incomes) > 0) {

                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        "email" => $this->identity()
                    ));

                    $createdOn = new \DateTime();

                    foreach ($incomes as $income) {

                        // if not granted
                        $criteria = array(
                            'defaultSalary' => $income,
                            'contract' => $target->getId()
                        );

                        $isGranted = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findBy($criteria);

                        if (count($isGranted) == 0) {
                            $entity = new NmtHrSalary();

                            /**@var \Application\Entity\NmtHrSalaryDefault $salaryDefault ; */
                            $salaryDefault = $this->doctrineEM->find('Application\Entity\NmtHrSalaryDefault', $income);

                            if ($salaryDefault instanceof \Application\Entity\NmtHrSalaryDefault) {

                                $entity->setDefaultSalary($salaryDefault);
                                $entity->setSalaryName($salaryDefault->getSalaryName());
                                $entity->setDecoratorFactory($salaryDefault->getDecoratorFactory());
                                $entity->setIsPayable($salaryDefault->getIsPayable());
                                $entity->setIsPitPayable($salaryDefault->getIsPitPayable());
                                $entity->setIsSsoPayable($salaryDefault->getIsSsoPayable());
                                $entity->setPaymentFrequency($salaryDefault->getPaymentFrequency());
                                $entity->setSalaryFactory($salaryDefault->getSalaryFactory());

                                $entity->setSalaryAmount(0);
                                $entity->setContract($target);
                                $entity->setEmployee($target->getEmployee());
                                $entity->setCreatedOn($createdOn);
                                $entity->setCreatedBy($u);

                                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

                                $this->doctrineEM->persist($entity);
                                $this->doctrineEM->flush();

                                $m = sprintf('%s #%s for Contract #%s - %s added. OK', $entity->getSalaryName(), $entity->getId(), $target->getId(), $target->getEmployee()->getEmployeeName());

                                // Trigger: hr.activity.log. AbtractController is EventManagerAware.
                                $this->getEventManager()->trigger('hr.activity.log', __METHOD__, array(
                                    'priority' => \Zend\Log\Logger::INFO,
                                    'message' => $m,
                                    'createdBy' => $u,
                                    'createdOn' => $createdOn,
                                    'entity_id' => $entity->getId(),
                                    'entity_class' => get_class($entity),
                                    'entity_token' => $entity->getToken()
                                ));
                            }
                        }
                    }
                }

                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        // NO POST
        // +++++++++++++++++++++++

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtHrContract $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtHrContract) {

            $entity = new NmtHrSalary();
            $entity->setContract($target);
            $entity->setEmployee($target->getEmployee());

            $criteria = array(
                'isActive' => 1
            );

            /**@var \Application\Repository\NmtHrEmployeeRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee');

            // $incomes = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalaryDefault')->findby($criteria);
            $incomes = $res->getNoneComponentOfContract($id, $token);

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $target,
                'incomes' => $incomes
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * Edit an salary Compoent
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
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

            /**@var \Application\Entity\NmtHrSalary $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findOneBy($criteria);
            $errors = array();

            if (! $entity instanceof \Application\Entity\NmtHrSalary) {

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
                $salaryAmount = $request->getPost('salaryAmount');

                $isPitPayable = (int) $request->getPost('isPitPayable');
                $isSsoPayable = (int) $request->getPost('isSsoPayable');

                if (! is_numeric($salaryAmount)) {
                    $errors[] = sprintf('salaryAmount "%s" is not valid. It must be a number.', $salaryAmount);
                } else {
                    if ($salaryAmount < 0) {
                        $errors[] = sprintf('salaryAmount "%s" is not valid. It must be greate than 0!', $salaryAmount);
                    }
                    $entity->setSalaryAmount($salaryAmount);
                }

                if ($isPitPayable != 1) {
                    $isPitPayable = 0;
                }

                if ($isSsoPayable != 1) {
                    $isSsoPayable = 0;
                }
                $entity->setIsPitPayable($isPitPayable);
                $entity->setIsSsoPayable($isSsoPayable);

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $errors[] = 'Nothing changed!';
                }

                if (count($errors) > 0) {

                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'target' => $entity->getContract()
                    ));
                }

                // NO ERROR
                // +++++++++++++++++++++++++++++++++

                $changeOn = new \DateTime();

                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));

                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();

                $m = sprintf('%s #%s - Contract #%s - %s updated. No. of change: %s. OK!', $entity->getSalaryName(), $entity->getId(), $entity->getContract()->getId(), $entity->getContract()
                    ->getEmployee()
                    ->getEmployeeName(), count($changeArray));

                // trigger log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('hr.contract.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => $entity->getRevisionNo(),
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn
                ));

                // Trigger: finance.activity.log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('hr.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn,
                    'entity_id' => $entity->getId(),
                    'entity_class' => get_class($entity),
                    'entity_token' => $entity->getToken()
                ));

                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        // NO POST
        // ++++++++++++++++++++++

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

        /**@var \Application\Entity\NmtHrSalary $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findOneBy($criteria);

        if ($entity instanceof \Application\Entity\NmtHrSalary) {

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getContract()
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function simulateAction()
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

        /**@var \Application\Entity\NmtHrContract $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtHrContract) {

            $criteria = array(
                'contract' => $target_id
            );
            $incomes = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findby($criteria);

            if (count($incomes) > 0) {

                Try {
                    $incomeList = array();
                    $employee = new Employee($target->getEmployee()->getEmployeeCode(), $target->getEmployee()->getEmployeeName());

                    $employee->setStatus($target->getContractStatus());
                    $employee->setStartWorkingdate($target->getEffectiveFrom());

                    $input = new ConsolidatedPayrollInput($employee, new \Datetime('2018-01-01'), new \Datetime('2018-01-31 10:30:00'));
                    $input->setactualworkeddays(24);
                    $input->setpaidsickleaves(0);
                    $input->settotalworkingdays(26);
                    $ytd = 2018;

                    foreach ($incomes as $income) {

                        /**@var \Application\Entity\NmtHrSalary $income ; */

                        $salaryFactory = $income->getSalaryFactory();
                        $incomeFactory = new $salaryFactory($income->getSalaryAmount(), "LAK");

                        if ($incomeFactory instanceof AbstractIncomeFactory) {
                            $incomeComponent = $incomeFactory->createIncomeComponent();
                            $incomeList[] = $incomeComponent;
                        }
                    }

                    $payroll = new Payroll($employee, $input, $incomeList);
                    $payroll->calculate();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

            return new ViewModel(array(
                'target' => $target,
                'list' => $incomes,
                'total_records' => count($incomes),
                'paginator' => null
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * Show an salary Compoent
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        return new ViewModel();
    }

    /**
     * Show an salary Compoent
     * Ajax accepted only
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

        /**@var \Application\Entity\NmtHrContract $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtHrContract) {

            $criteria = array(
                'contract' => $target_id
            );
            $incomes = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findby($criteria);

            return new ViewModel(array(
                'target' => $target,
                'list' => $incomes,
                'total_records' => count($incomes),
                'paginator' => null
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * List all active salary component of an contract revision.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        return new ViewModel();
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}

<?php
namespace Finance\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcurePo;
use Application\Entity\FinVendorInvoiceRowTmp;
use Application\Entity\FinFx;

/**
 * 02/07
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FxController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    /**
     * adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {

        // $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $request = $this->getRequest();

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $fxDate = $request->getPost('fxDate');
            $fxRate = $request->getPost('fxRate');

            $source_currency_id = (int) $request->getPost('source_currency_id');
            $target_currency_id = (int) $request->getPost('target_currency_id');

            $entity = new FinFx();
            $entity->setIsActive(1);

            $source_currency = null;
            if ($source_currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $source_currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($source_currency_id);
            }

            if ($source_currency !== null) {
                $entity->setSourceCurrency($source_currency);
            } else {
                $errors[] = 'Source Currency can\'t be empty. Please select a Currency!';
            }

            $target_currency = null;
            if ($target_currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $target_currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($target_currency_id);
            }

            if ($target_currency !== null) {
                $entity->setTargetCurrency($target_currency);
            } else {
                $errors[] = 'Target Currency can\'t be empty. Please select a Currency!';
            }

            $validator = new Date();

            if (! $validator->isValid($fxDate)) {
                $errors[] = 'FX Date is not correct or empty!';
            } else {
                $entity->setfxDate(new \DateTime($fxDate));
            }

            if ($fxRate !== null) {
                if (! is_numeric($fxRate)) {
                    $errors[] = 'FX rate is not valid. It must be a number.';
                } else {
                    if ($fxRate <= 0) {
                        $errors[] = 'FX rate must be greate than 0!';
                    }
                    $entity->setFxRate($fxRate);
                }
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list
                ));
            }

            // NO ERROR
            // ++++++++++++++++++++++++++

            $createdOn = new \DateTime();

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

            $company_id = 1;
            $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
            $entity->setCompany($company);

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $m = sprintf('[OK]  FX Rate %s - %s created', $entity->getSourceCurrency()->getCurrency(), $entity->getTargetCurrency()->getCurrency());
            $this->flashMessenger()->addMessage($m);

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            // $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            $redirectUrl = "/finance/fx/list";

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NOT POST
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        // get company

        $entity = new FinFx();
        $entity->setIsActive(1);

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
            $entity->setTargetCurrency($default_cur);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list
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
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\FinFx')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\FinFx) {

            /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
            $nmtPlugin = $this->Nmtplugin();
            $currency_list = $nmtPlugin->currencyList();

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Edit Invoice Header
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\FinFx $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinFx')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\FinFx) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'n' => $nTry
                ));

                // might need redirect
            } else {

                $oldEntity = clone ($entity);

                $fxDate = $request->getPost('fxDate');
                $fxRate = $request->getPost('fxRate');
                $source_currency_id = (int) $request->getPost('source_currency_id');
                $target_currency_id = (int) $request->getPost('target_currency_id');

                $source_currency = null;
                if ($source_currency_id > 0) {
                    /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                    $source_currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($source_currency_id);
                }

                if ($source_currency instanceof \Application\Entity\NmtApplicationCurrency) {
                    $entity->setSourceCurrency($source_currency);
                } else {
                    $errors[] = 'Source Currency can\'t be empty. Please select a Currency!';
                }

                $target_currency = null;
                if ($target_currency_id > 0) {
                    /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                    $target_currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($target_currency_id);
                }

                if ($target_currency instanceof \Application\Entity\NmtApplicationCurrency) {
                    $entity->setTargetCurrency($target_currency);
                } else {
                    $errors[] = 'Target Currency can\'t be empty. Please select a Currency!';
                }

                $validator = new Date();

                if (! $validator->isValid($fxDate)) {
                    $errors[] = 'FX Date is not correct or empty!';
                } else {
                    $entity->setfxDate(new \DateTime($fxDate));
                }

                if ($fxRate !== null) {
                    if (! is_numeric($fxRate)) {
                        $errors[] = 'FX rate is not valid. It must be a number.';
                    } else {
                        if ($fxRate <= 0) {
                            $errors[] = 'FX rate must be greate than 0!';
                        }
                        $entity->setFxRate($fxRate);
                    }
                }

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }

                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "FX #%s"?', $entity->getId());
                }

                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "FX #%s". Please try later!', $entity->getId());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'currency_list' => $currency_list,
                        'n' => $nTry
                    ));
                }

                // NO ERROR
                // ===================

                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));

                $changeOn = new \DateTime();

                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();

                $m = sprintf('[OK] FX #%s updated. Change No.: %s.', $entity->getId(), count($changeArray));

                // Trigger Change Log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('finance.change.log', __METHOD__, array(
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
                $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
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
        // ====================

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

        /**@var \Application\Entity\FinVendorInvoice $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\FinFx')->findOneBy($criteria);

        if ($entity instanceof \Application\Entity\FinFx) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'currency_list' => $currency_list,
                'n' => 0
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
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
            $sort_by = "fxDate";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
        // $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
        // $postingPeriod = $p->getPostingPeriodStatus(new \DateTime());
        // echo $postingPeriod->getPeriodName() . $postingPeriod->getPeriodStatus();
        // echo $postingPeriod;

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $criteria = array();

        $sort_criteria = array(
            'fxDate' => 'DESC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\FinFx')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\FinFx')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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

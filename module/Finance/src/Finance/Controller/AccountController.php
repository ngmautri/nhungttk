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
use Application\Entity\FinAccount;

/**
 * 02/07
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AccountController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     * Adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        // $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $accountNumber = $request->getPost('accountNumber');
            $accountType = $request->getPost('accountType');

            $isActive = (int) $request->getPost('isActive');
            $description = $request->getPost('description');

            $entity = new FinAccount();
            if ($isActive !== 1) {
                $isActive = 0;
            }

            $entity->setAccountNumber($accountNumber);
            $entity->setAccountType($accountType);
            $entity->setDescription($description);
            $entity->setIsActive($isActive);
            
            if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
                $entity->setCompany($u->getCompany());
            } else {
                $errors[] = 'Company is not defined!';
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
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $createdOn = new \DateTime();

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $m = sprintf('[OK] G/L Account #%s created', $entity->getId());
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

            $redirectUrl = "/finance/account/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity = new FinAccount();
        $entity->setIsActive(1);

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
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $invoice = $res->getVendorInvoice($id, $token);

        if ($invoice == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }

        if ($entity instanceof FinVendorInvoice) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $invoice['total_row'],
                'active_row' => $invoice['active_row'],
                'max_row_number' => $invoice['total_row'],
                'total_picture' => $invoice['total_picture'],
                'total_attachment' => $invoice['total_attachment'],
                'net_amount' => $invoice['net_amount'],
                'tax_amount' => $invoice['tax_amount'],
                'gross_amount' => $invoice['gross_amount']
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

            /**@var \Application\Entity\FinVendorInvoice $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\FinVendorInvoice) {

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

                $redirectUrl = $request->getPost('redirectUrl');

                $contractDate = $request->getPost('contractDate');
                $contractNo = $request->getPost('contractNo');
                $currentState = $request->getPost('currentState');

                $vendor_id = (int) $request->getPost('vendor_id');
                $currency_id = (int) $request->getPost('currency_id');
                $warehouse_id = (int) $request->getPost('target_wh_id');

                $postingDate = $request->getPost('postingDate');
                $grDate = $request->getPost('grDate');
                $invoiceDate = $request->getPost('invoiceDate');
                $invoiceNo = $request->getPost('invoiceNo');
                $sapDoc = $request->getPost('sapDoc');
                $isActive = (int) $request->getPost('isActive');
                $remarks = $request->getPost('remarks');

                if ($isActive !== 1) {
                    $isActive = 0;
                }

                if ($sapDoc == "") {
                    $sapDoc = "N/A";
                }

                $entity->setIsActive($isActive);

                $entity->setCurrentState($currentState);

                $vendor = null;
                if ($vendor_id > 0) {
                    $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
                }

                if ($vendor !== null) {
                    $entity->setVendor($vendor);
                } else {
                    $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
                }

                $currency = null;
                if ($currency_id > 0) {
                    $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
                }

                if ($currency !== null) {
                    $entity->setCurrency($currency);
                } else {
                    $errors[] = 'Currency can\'t be empty. Please select a vendor!';
                }

                $validator = new Date();

                if ($invoiceNo == null) {
                    $errors[] = 'Please enter Invoice Number!';
                } else {
                    $entity->setInvoiceNo($invoiceNo);
                }

                $entity->setSapDoc($sapDoc);

                if ($invoiceDate !== null) {

                    if (! $validator->isValid($invoiceDate)) {
                        $errors[] = 'Invoice Date is not correct or empty!';
                    } else {
                        $entity->setInvoiceDate(new \DateTime($invoiceDate));
                    }
                }

                if ($postingDate !== null) {
                    if (! $validator->isValid($postingDate)) {
                        $errors[] = 'Posting Date is not correct or empty!';
                    } else {

                        $entity->setPostingDate(new \DateTime($postingDate));
                    }
                }

                if ($grDate !== null) {
                    if (! $validator->isValid($grDate)) {
                        $errors[] = 'Good receipt Date is not correct or empty!';
                    } else {
                        $entity->setGrDate(new \DateTime($grDate));
                    }
                }

                $warehouse = null;
                if ($warehouse_id > 0) {
                    $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
                }

                if ($warehouse !== null) {
                    $entity->setWarehouse($warehouse);
                } else {
                    $errors[] = 'Warehouse can\'t be empty. Please select a vendor!';
                }

                $entity->setRemarks($remarks);

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }

                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "AP Inv. %s (%s)"?', $entity->getInvoiceNo(), $entity->getSysNumber());
                }

                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "AP Inv. %s (%s)". Please try later!', $entity->getInvoiceNo(), $entity->getSysNumber());
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

                $m = sprintf('[OK] AP Invoice #%s - %s updated. Change No.: %s.', $entity->getId(), $entity->getSysNumber(), count($changeArray));

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

                /**
                 * === Update current state of row invoice row ===
                 */

                $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\FinVendorInvoiceRow r SET r.currentState = :new_state WHERE r.invoice =:invoice_id
                    ')->setParameters(array(
                    'new_state' => $entity->getCurrentState(),
                    'invoice_id' => $entity->getId()
                ));
                $query->getResult();

                /**
                 * === Update current state of stock row.
                 * ===
                 */

                $criteria = array(
                    'isActive' => 1,
                    'invoice' => $entity->getId()
                );
                $sort_criteria = array();
                $invoice_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria, $sort_criteria);
                if (count($invoice_rows) > 0) {
                    foreach ($invoice_rows as $r) {
                        $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\NmtInventoryTrx t SET t.currentState = :new_state, t.isActive=:is_active WHERE t.invoiceRow =:invoice_row_id
                    ')->setParameters(array(
                            'new_state' => $r->getCurrentState(),
                            'is_active' => 1,
                            'invoice_row_id' => $r->getId()
                        ));
                        $query->getResult();
                    }
                }

                $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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
        $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

        if ($entity instanceof \Application\Entity\FinVendorInvoice) {
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
            $sort_by = "accountNumber";
        endif;

        if ($sort == null) :
            $sort = "ASC";
        endif;

        /**@var \Application\Repository\FinAccountRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinAccount');
        $list = $res->getAccountList($is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getAccountList($is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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

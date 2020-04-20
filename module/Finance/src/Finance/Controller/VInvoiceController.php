<?php
namespace Finance\Controller;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Entity\FinVendorInvoiceRowTmp;
use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcurePo;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 * 02/07
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class VInvoiceController extends AbstractActionController
{

    protected $doctrineEM;

    protected $apService;

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function reverseAction()
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        $request = $this->getRequest();

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

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];

            $reversalDate = $data['reversalDate'];
            $reversalReason = $data['reversalReason'];

            $id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('token');

            /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
            $invoice = $res->getVendorInvoice($entity_id, $entity_token);

            if ($invoice == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $entity = null;
            if ($invoice[0] instanceof FinVendorInvoice) {
                $entity = $invoice[0];
            }

            if ($entity == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $viewModel = new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => null,
                'active_row' => null,
                'max_row_number' => null,
                'total_picture' => null,
                'total_attachment' => null,
                'net_amount' => null,
                'tax_amount' => null,
                'gross_amount' => null
            ));

            $errors = $this->apService->reverseAP($entity, $u, $reversalDate, $reversalReason, __METHOD__);

            if (count($errors) > 0) {

                $m = $nmtPlugin->translate("Reversal failed!");
                $this->flashMessenger()->addMessage($m);

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
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

                $viewModel->setTemplate("finance/v-invoice/reverse");
                return $viewModel;
            }

            $m = sprintf("AP #%s reversed", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/finance/v-invoice/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
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
        } else {
            return $this->redirect()->toRoute('access_denied');
        }

        $errors = array();
        if ($entity->getIsReversable() == 11) {
            $errors[] = 'Invoice is not reservable, becasue sequence document is created.';
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => $errors,
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
    }

    /**
     * Adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        $wh_list = $nmtPlugin->warehouseList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new FinVendorInvoice();
            $entity->setLocalCurrency($default_cur);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);

            $errors = $this->apService->saveHeader($entity, $data, $u, TRUE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'wh_list' => $wh_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] A/P Invoice #%s created', $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/finance/v-invoice-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
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

        $entity = new FinVendorInvoice();
        $entity->setIsActive(1);

        // Default currency
        if ($default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
            $entity->setCurrency($default_cur);
        }

        $entity->setCurrentState('finalInvoice');

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'wh_list' => $wh_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("finance/v-invoice/crud");
        return $viewModel;
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function add1Action()
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
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $wh_list = $nmtPlugin->warehouseList();
        $incoterm_list = $nmtPlugin->incotermList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $id = (int) $data['entity_id'];
            $token = $data['token'];

            $invoice = $res->getVendorInvoice($id, $token);

            $entity = null;
            if ($invoice[0] instanceof FinVendorInvoice) {
                $entity = $invoice[0];
            }

            if (! $entity instanceof FinVendorInvoice) {
                return $this->redirect()->toRoute('access_denied');
            }

            // ========================

            if ($entity->getLocalCurrency() == null) {
                $entity->setLocalCurrency($default_cur);
            }

            $errors = $this->apService->post($entity, $data, $u, true, true, __METHOD__);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'wh_list' => $wh_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin,

                    'total_row' => $invoice['total_row'],
                    'active_row' => $invoice['active_row'],
                    'max_row_number' => $invoice['total_row'],
                    'total_picture' => $invoice['total_picture'],
                    'total_attachment' => $invoice['total_attachment'],
                    'net_amount' => $invoice['net_amount'],
                    'tax_amount' => $invoice['tax_amount'],
                    'gross_amount' => $invoice['gross_amount']
                ));
            }

            $m = sprintf("[OK] AP invoice %s posted.", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            // $redirectUrl = "/finance/v-invoice/list";

            $redirectUrl = sprintf("/finance/v-invoice/show?nid=%s&entity_id=%s&token=%s", \Application\Model\Constants::v4(), $entity->getId(), $entity->getToken());
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================
        if ($request->getHeader('Referer') == null) {
            $redirectUrl = null;
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $invoice = $res->getVendorInvoice($id, $token);

        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            $m = sprintf('AP Invoice #%s - %s already posted!', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        if ($invoice['active_row'] == 0) {
            $m = sprintf('[INFO] AP Invoice #%s has no lines.', $entity->getSysNumber());
            $m1 = $nmtPlugin->translate('Document is incomplete!');
            $this->flashMessenger()->addMessage($m1);
            $redirectUrl = "/finance/v-invoice-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'currency_list' => $currency_list,
            'wh_list' => $wh_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin,

            'total_row' => $invoice['total_row'],
            'active_row' => $invoice['active_row'],
            'max_row_number' => $invoice['total_row'],
            'total_picture' => $invoice['total_picture'],
            'total_attachment' => $invoice['total_attachment'],
            'net_amount' => $invoice['net_amount'],
            'tax_amount' => $invoice['tax_amount'],
            'gross_amount' => $invoice['gross_amount']
        ));
    }

    /**
     * Make A/P Invoice from PO
     * case GR-IR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $id = (int) $request->getPost('target_id');
            $token = $request->getPost('target_token');

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($id, $token);

            /**@var \Application\Entity\NmtProcurePo $target ;*/
            $target = null;

            if ($po != null) {
                if ($po[0] instanceof NmtProcurePo) {
                    $target = $po[0];
                }
            }

            if ($target == null) {
                $errors[] = 'Contract /PO can\'t be empty!';
                $this->flashMessenger()->addMessage('Something wrong!');

                if (count($errors) > 0) {
                    $viewModel = new ViewModel(array(
                        'action' => \Application\Model\Constants::FORM_ACTION_AP_FROM_PO,
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => null,
                        'target' => null,
                        'nmtPlugin' => $nmtPlugin
                    ));

                    $viewModel->setTemplate("finance/v-invoice/crud");
                    return $viewModel;
                }
            }

            $entity = new FinVendorInvoice();
            $entity->setLocalCurrency($default_cur);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->apService->saveHeader($entity, $data, $u, TRUE);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_AP_FROM_PO,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            try {
                $this->apService->copyFromPO($entity, $target, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_AP_FROM_PO,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            $m = sprintf("[OK] AP #%s created from P/O #%s", $entity->getSysNumber(), $target->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/finance/v-invoice/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {

            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Entity\NmtProcurePo $target ;*/

        $target = null;
        if ($po[0] instanceof NmtProcurePo) {
            $target = $po[0];
        }

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new FinVendorInvoice();
        $entity->setContractNo($target->getContractNo());
        $entity->setContractDate($target->getContractDate());
        $entity->setVendor($target->getVendor());
        $entity->setCurrency($target->getCurrency());
        $entity->setExchangeRate($target->getExchangeRate());

        $entity->setLocalCurrency($target->getLocalCurrency());
        $entity->setDocCurrency($target->getDocCurrency());

        $entity->setIsActive(1);

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_AP_FROM_PO,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("finance/v-invoice/crud");
        return $viewModel;
    }

    /**
     * Make A/P Invoice from GR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromGrAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $id = (int) $request->getPost('target_id');
            $token = $request->getPost('target_token');

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $gr = $res->getGr($id, $token);

            /**@var \Application\Entity\NmtProcureGr $target ;*/
            $target = null;

            if ($gr != null) {
                if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
                    $target = $gr[0];
                }
            }

            if (! $target instanceof \Application\Entity\NmtProcureGr) {
                $errors[] = 'Goods Receipt Object is not found!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list
                ));
            }

            $validator = new Date();

            $contractDate = $request->getPost('contractDate');
            $contractNo = $request->getPost('contractNo');
            $currentState = $request->getPost('currentState');

            $vendor_id = (int) $request->getPost('vendor_id');
            $currency_id = (int) $request->getPost('currency_id');

            $postingDate = $request->getPost('postingDate');
            // $grDate = $request->getPost('grDate');
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

            $entity = new FinVendorInvoice();

            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setIsActive($isActive);
            $entity->setCurrentState($currentState);

            if (! $contractDate == null) {
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
            }
            $entity->setContractNo($contractNo);

            $vendor = null;
            if ($vendor_id > 0) {
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }

            if ($vendor instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($vendor);
                $entity->setVendorName($vendor->getVendorName());
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
            }

            $currency = null;
            if ($currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }

            if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }

            // check later
            $entity->setInvoiceNo($invoiceNo);

            $entity->setSapDoc($sapDoc);

            if (! $invoiceDate == null) {
                if (! $validator->isValid($invoiceDate)) {
                    $errors[] = 'Invoice Date is not correct or empty!';
                } else {
                    $entity->setInvoiceDate(new \DateTime($invoiceDate));
                }
            }

            // check if posting period is close
            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

            // check one more time
            if (! $postingDate == null) {
                if (! $validator->isValid($postingDate)) {
                    $errors[] = 'Posting Date is not correct or empty!';
                } else {

                    $entity->setPostingDate(new \DateTime($postingDate));

                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));

                    if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                        $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                    } else {
                        if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                            $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                        }
                    }
                }
            }

            // check one more time

            $entity->setRemarks($remarks);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list
                ));
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));

            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

            $this->doctrineEM->persist($entity);

            try {
                $this->apService->copyFromGR($entity, $target, $u, true);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list
                ));
            }

            $m = sprintf("[OK] AP #%s created from P/O #%s", $entity->getSysNumber(), $target->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/finance/v-invoice/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {

            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);

        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Entity\NmtProcureGr $target ;*/

        $target = null;
        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            $target = $gr[0];
        }

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new FinVendorInvoice();
        $entity->setContractNo($target->getContractNo());
        $entity->setContractDate($target->getContractDate());

        $entity->setIsActive(1);

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list
        ));
    }

    /**
     * Make A/P Invoice from PO
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function acceptFromPoAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $rows_tmp = $res->getAPInvoiceTmp($id, $token);

        if ($rows_tmp !== null) {

            /**@var \Application\Entity\FinVendorInvoice $target ;*/
            $target = null;

            if (count($rows_tmp) > 0) {
                $row_1 = $rows_tmp[0];
                if ($row_1 instanceof FinVendorInvoiceRowTmp) {
                    $target = $row_1->getInvoice();
                }

                if ($target == null) {
                    return $this->redirect()->toRoute('access_denied');
                }

                $n = 0;
                foreach ($rows_tmp as $r) {

                    /**@var \Application\Entity\FinVendorInvoiceRowTmp $r ;*/

                    $n ++;
                    $ap_row = new FinVendorInvoiceRow();
                    $ap_row->setIsActive(1);
                    $ap_row->setRowNumber($n);
                    $ap_row->setRowIndentifer($target->getSysNumber() . "-$n");

                    $ap_row->setCurrentState($target->getCurrentState());
                    $ap_row->setInvoice($target);
                    $ap_row->setPoRow($r->getPoRow());
                    $ap_row->setPrRow($r->getPrRow());
                    $ap_row->setItem($r->getItem());

                    $ap_row->setQuantity($r->getQuantity());
                    $ap_row->setUnit($r->getUnit());
                    $ap_row->setUnitPrice($r->getUnitPrice());

                    $netAmount = $r->getQuantity() * $r->getUnitPrice();

                    $taxRate = (int) $r->getTaxRate();
                    $ap_row->setTaxRate($taxRate);

                    $taxAmount = $netAmount * $taxRate;
                    $grossAmount = $netAmount + $taxAmount;

                    $ap_row->setNetAmount($netAmount);
                    $ap_row->setGrossAmount($grossAmount);

                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        "email" => $this->identity()
                    ));

                    $ap_row->setCreatedBy($u);
                    $ap_row->setCreatedOn(new \DateTime());
                    $ap_row->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                    $ap_row->setRemarks($r->getRemarks());

                    $this->doctrineEM->persist($ap_row);

                    $r->setCurrentState("ACCEPTED");
                    $r->setIsActive(0);
                    $this->doctrineEM->persist($r);

                    $gr_entity = new NmtInventoryTrx();
                    $gr_entity->setVendor($target->getVendor());
                    $gr_entity->setFlow('IN');
                    $gr_entity->setInvoiceRow($ap_row);
                    $gr_entity->setItem($r->getItem());
                    $gr_entity->setPrRow($r->getPrRow());
                    $gr_entity->setQuantity($r->getQuantity());
                    $gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $gr_entity->setVendorItemUnit($r->getUnit());
                    $gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $gr_entity->setTrxDate($target->getGrDate());
                    $gr_entity->setCurrency($target->getCurrency());
                    $gr_entity->setRemarks("GR of Invoice " . $target->getInvoiceNo());
                    $gr_entity->setWh($target->getWarehouse());
                    $gr_entity->setCreatedBy($u);
                    $gr_entity->setCreatedOn(new \DateTime());
                    $gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                    $gr_entity->setChecksum(Rand::getString(32, \Application\Model\Constants::CHAR_LIST, true));

                    $gr_entity->setTaxRate($r->getTaxRate());

                    $gr_entity->setCurrentState($target->getCurrentState());

                    if ($target->getCurrentState() == "finalInvoice") {
                        $gr_entity->setIsActive(1);
                    } else {
                        $gr_entity->setIsActive(0);
                    }

                    $this->doctrineEM->persist($gr_entity);
                    $this->doctrineEM->flush();
                }
            }

            $this->doctrineEM->flush();

            /*
             * $redirectUrl = "/finance/v-invoice/copy-from-po1?token=" . $target>getToken() . "&entity_id=" . $entity->getId();
             */
            $redirectUrl = "/finance/v-invoice/list";
            return $this->redirect()->toUrl($redirectUrl);

            /*
             * return new ViewModel(array(
             * 'target' => $target,
             * ));
             */
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function copyFromPo1Action()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );

        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);

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
        $invoice = $res->getVendorInvoiceTmp($id, $token);

        if ($invoice == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }

        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $invoice['total_row'],
                'active_row' => $invoice['active_row'],
                'max_row_number' => $invoice['total_row'],
                'net_amount' => $invoice['net_amount'],
                'tax_amount' => $invoice['tax_amount'],
                'gross_amount' => $invoice['gross_amount']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
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
        $this->layout("Finance/layout-fullscreen");
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        $wh_list = $nmtPlugin->warehouseList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = $data['entity_id'];
            $token = $data['token'];
            $nTry = (int) $data['n'] + 1;

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\FinVendorInvoice $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'wh_list' => $wh_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin,
                    'n' => $nTry
                ));
            }

            // ======= OK =======//

            // validate and update entity
            $ck = $this->apService->saveHeader($entity, $data, $u, FALSE, __METHOD__);

            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
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
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'nmtPlugin' => $nmtPlugin,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] AP Invoice #%s - %s updated.', $entity->getId(), $entity->getSysNumber());

            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // initiating..........
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

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'wh_list' => $wh_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin,
            'n' => 0
        ));

        $viewModel->setTemplate("finance/v-invoice/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response
     */
    public function alterAction()
    {
        $action = $this->params()->fromQuery('action');
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );

        /** @var \Application\Entity\NmtFinPostingPeriod $entity */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        if ($entity !== null) {

            if ($action == "open") {
                $entity->setPeriodStatus("N");
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            } elseif ($action == "close") {
                $entity->setPeriodStatus("C");
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            }
        }
        return $this->redirect()->toUrl('/finance/posting-period/list');
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
        $docStatus = $this->params()->fromQuery('docStatus');

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

        if ($docStatus == null) :
            $docStatus = "posted";

            if ($sort_by == null) :
                $sort_by = "sysNumber";
            endif;
        endif;


        if ($docStatus == 'draft') :

            if ($sort_by == null) :
                $sort_by = "createdOn";
            endif;
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
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $list = $res->getVendorInvoiceList($is_active, $currentState, $docStatus, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getVendorInvoiceList($is_active, $currentState, $docStatus, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus
        ));
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function vendorAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $vendor_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

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
            $sort_by = "InvoiceDate";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {

                /**@var \Application\Entity\FinVendorInvoice $entity ;*/

                if ($entity->getVendor() !== null) {
                    $entity->setVendorName($entity->getVendor()
                        ->getVendorName());
                }

                if ($entity->getCurrency() !== null) {
                    $entity->setCurrencyIso3($entity->getCurrency()
                        ->getCurrency());
                }

                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        return new ViewModel(array(
            'list' => $list
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

    /**
     *
     * @return \Procure\Service\APInvoiceService
     */
    public function getApService()
    {
        return $this->apService;
    }

    /**
     *
     * @param \Procure\Service\APInvoiceService $apService
     */
    public function setApService(\Procure\Service\APInvoiceService $apService)
    {
        $this->apService = $apService;
    }
}

<?php
namespace Inventory\Controller;

use Application\Entity\NmtInventoryMv;
use Application\Entity\NmtInventoryTrx;
use Doctrine\ORM\EntityManager;
use Inventory\Service\ItemSearchService;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 * Goods Issue
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransferController extends AbstractActionController
{

    protected $doctrineEM;

    protected $itemSearchService;

    protected $giService;

    protected $inventoryTransactionService;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $transactionType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $isAllowed = true;

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

            $entity = new NmtInventoryMv();
            $entity->setLocalCurrency($default_cur);
            $errors = $this->inventoryTransactionService->saveHeader($entity, $data, $u, TRUE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'form_action' => '/inventory/transfer/add',
                    'form_title' => $nmtPlugin->translate("Create Good Transfer"),

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin,
                    'transactionType' => $transactionType,
                    'isAllowed' => $isAllowed
                ));

                $viewModel->setTemplate("inventory/transfer/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Inventory transaction %s created.', $entity->getId());

            $redirectUrl = sprintf('/inventory/transfer-row/add?token=%s&target_id=%s', $entity->getToken(), $entity->getId());
            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        $redirectUrl = null;

        if ($request->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $this->layout("Inventory/layout-fullscreen");

        $movementType = $this->params()->fromQuery('movementType');
        $sourceWhId = (int) $this->params()->fromQuery('sourceWH');
        $transferDate = $this->params()->fromQuery('transferDate');

        $entity = new NmtInventoryMv();

        $criteria = array(
            'id' => $sourceWhID
        );

        $sourceWh = null;
        $location = null;

        $sourceWh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);

        if ($sourceWh !== null) {
            $criteria = array(
                'warehouse' => $sourceWh
            );

            $location = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->findBy($criteria);
        }

        $entity->setWarehouse($sourceWh);
        $entity->setMovementDate(new \Datetime($transferDate));
        $entity->setMovementType($movementType);

        $entity->setIsActive(1);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'form_action' => '/inventory/transfer/add',
            'form_title' => $nmtPlugin->translate("Create Good Transfer"),
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin,
            'transactionType' => $transactionType,
            'isAllowed' => $isAllowed,
            'location' => $location
        ));

        $viewModel->setTemplate("inventory/transfer/crud" . $movementType);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function reverseAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

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
            $entity_id = (int) $data['entity_id'];
            $entity_token = $data['entity_token'];

            $reversalDate = $data['reversalDate'];
            $reversalReason = $data['reversalReason'];

            /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
            $entity_array = $res->getMovement($entity_id, $entity_token);

            if ($entity_array == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $entity = null;
            if ($entity_array[0] instanceof NmtInventoryMv) {
                $entity = $entity_array[0];
            }

            if ($entity == null) {
                $m = $nmtPlugin->translate("WH Transaction not found. Please check.");
                $errors[] = $m;
                $this->flashMessenger()->addMessage($m);

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => null,
                    'errors' => $errors,
                    'issueType' => $issueType,
                    'movementTypeInfo' => null
                ));

                $viewModel->setTemplate("inventory/gi/reverse");
                return $viewModel;
            }

            $movementTypeInfo = '';
            $giType = \Inventory\Model\Constants::getGoodsIssueType($entity->getMovementType(), $nmtPlugin->getTranslator());
            if ($giType !== null) {
                $movementTypeInfo = $giType['type_description'];
            }

            $errors = $this->inventoryTransactionService->reverse($entity, $u, $reversalDate, $reversalReason, __METHOD__);

            if (count($errors) > 0) {

                $m = $nmtPlugin->translate("Reversal failed!");
                $this->flashMessenger()->addMessage($m);

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'issueType' => $issueType,
                    'movementTypeInfo' => $movementTypeInfo
                ));

                $viewModel->setTemplate("inventory/gi/reverse");
                return $viewModel;
            }

            $m = sprintf("WH GI #%s reversed", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            // $redirectUrl = "/inventory/gi/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $gi = $res->getMovement($id, $token);

        if ($gi == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $entity = $gi[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $movementTypeInfo = '';
        $giType = \Inventory\Model\Constants::getGoodsIssueType($entity->getMovementType(), $nmtPlugin->getTranslator());
        if ($giType !== null) {
            $movementTypeInfo = $giType['type_description'];
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'issueType' => $issueType,
            'movementTypeInfo' => $movementTypeInfo
        ));
    }

    /**
     *
     * @return \Inventory\Service\InventoryTransactionService
     */
    public function getInventoryTransactionService()
    {
        return $this->inventoryTransactionService;
    }

    /**
     *
     * @param \Inventory\Service\InventoryTransactionService $inventoryTransactionService
     */
    public function setInventoryTransactionService(\Inventory\Service\InventoryTransactionService $inventoryTransactionService)
    {
        $this->inventoryTransactionService = $inventoryTransactionService;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes();

        foreach ($issueType as $t) {
            var_dump($t);
        }

        $viewModel = new ViewModel(array(
            'redirectUrl' => null
        ));
        $viewModel->setTemplate("inventory/gi/index1");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        // $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $transactionType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $gi = $res->getMovement($id, $token);

        if ($gi == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $entity = $gi[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $movementTypeInfo = '';
        $giType = \Inventory\Model\Constants::getGoodsIssueType($entity->getMovementType(), $nmtPlugin->getTranslator());
        if ($giType !== null) {
            $movementTypeInfo = $giType['type_description'];
        }

        $viewModel = new ViewModel(array(

            'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
            'form_action' => '',
            'form_title' => $nmtPlugin->translate("Show Goods Transfer"),

            'nmtPlugin' => $nmtPlugin,
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'transactionType' => $transactionType,
            'movementTypeInfo' => $movementTypeInfo
        ));

        $viewModel->setTemplate("inventory/transfer/show");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $transactionType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

        $isAllowed = true;

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
            $entity_id = (int) $data['entity_id'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id
            );

            /** @var \Application\Entity\NmtInventoryMv $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->findOneBy($criteria);

            if ($entity == null) {
                $errors[] = 'Entity not found or emty!';

                $this->flashMessenger()->addMessage('Something wrong!');
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'form_action' => '/inventory/transfer/edit',
                    'form_title' => $nmtPlugin->translate("Edit Goods Transfer"),

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin,
                    'transactionType' => $transactionType,
                    'isAllowed' => $isAllowed
                ));

                $viewModel->setTemplate("inventory/transfer/crud");
                return $viewModel;
            }

            $checkALC = $nmtPlugin->isParent($u, $entity->getCreatedBy());
            if (isset($checkALC['result']) and isset($checkALC['message'])) {
                if ($checkALC['result'] == 0) {
                    $errors[] = $nmtPlugin->translate("No authority to perform this operation on this object! Only the owner or the administrator has the right to do it.");
                    $isAllowed = false;
                }
            } else {
                $errors[] = $nmtPlugin->translate("ACL checking failed");
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'form_action' => '/inventory/transfer/edit',
                    'form_title' => $nmtPlugin->translate("Edit Goods Transfer"),

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin,
                    'transactionType' => $transactionType,
                    'isAllowed' => $isAllowed
                ));

                $viewModel->setTemplate("inventory/transfer/crud");
                return $viewModel;
            }

            $nTry ++;

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit (%s)?', $entity->getId());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit (%s). Please try later!', $entity->getId());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if ($entity->getLocalCurrency() == null) {
                $entity->setLocalCurrency($default_cur);
            }

            $errors = $this->inventoryTransactionService->saveHeader($entity, $data, $u, FALSE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'form_action' => '/inventory/transfer/edit',
                    'form_title' => $nmtPlugin->translate("Edit Goods Transfer"),

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin,
                    'transactionType' => $transactionType,
                    'isAllowed' => $isAllowed
                ));

                $viewModel->setTemplate("inventory/item-transaction/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Inventory transaction %s updated.', $entity->getId());

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;

        if ($this->getRequest()->getHeader('Referer') != null) {
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

        /** @var \Application\Entity\NmtInventoryMv $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->findOneBy($criteria);

        $checkALC = $nmtPlugin->isParent($u, $entity->getCreatedBy());

        $errors = array();
        if (isset($checkALC['result']) and isset($checkALC['message'])) {
            if ($checkALC['result'] == 0) {
                $errors[] = $nmtPlugin->translate("No authority to perform this operation on this object! Only the owner or the administrator has the right to do it.");
                $isAllowed = false;
            }
        } else {
            $errors[] = $nmtPlugin->translate("ACL checking failed");
        }

        // only update remark posible, when posted.
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED or $entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_REVERSED) {
            $errors[] = $nmtPlugin->translate(" can not change.");

            // $isAllowed = false;
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'form_action' => '/inventory/transfer/edit',
            'form_title' => $nmtPlugin->translate("Edit Goods Transfer"),

            'errors' => $errors,
            'entity' => $entity,
            'redirectUrl' => $redirectUrl,
            'n' => 0,
            'nmtPlugin' => $nmtPlugin,
            'transactionType' => $transactionType,
            'isAllowed' => $isAllowed
        ));

        $viewModel->setTemplate("inventory/transfer/crud");
        return $viewModel;
    }

    /**
     * Review and Post Transfer.
     * Document can't be changed.
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

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
            $entity_id = (int) $data['entity_id'];
            $entity_token = $data['entity_token'];

            /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
            $entity_array = $res->getMovement($entity_id, $entity_token);

            if ($entity_array == null) {
                $errors[] = "Entity not found!";
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'issueType' => $issueType,
                    'movementTypeInfo' => null,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            /**@var \Application\Entity\NmtInventoryMv $entity ;*/

            $entity = null;
            if ($entity_array[0] instanceof NmtInventoryMv) {
                $entity = $entity_array[0];
            }

            if ($entity == null) {
                $errors[] = "Entity not found!";
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'issueType' => $issueType,
                    'movementTypeInfo' => null,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $movementTypeInfo = '';
            $giType = \Inventory\Model\Constants::getGoodsIssueType($entity->getMovementType(), $nmtPlugin->getTranslator());
            if ($giType !== null) {
                $movementTypeInfo = $giType['type_description'];
            }

            $errors = $this->inventoryTransactionService->postTransfer($entity, $data, $u, true, true, __METHOD__);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'issueType' => $issueType,
                    'movementTypeInfo' => $movementTypeInfo,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $m = sprintf("[OK] Goods Movement: %s posted!", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/inventory/item-transaction/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        $redirectUrl = null;
        /*
         * if ($request->getHeader('Referer') !== null) {
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         * }
         */

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $gi = $res->getMovement($id, $token);

        if ($gi == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $entity = $gi[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $movementTypeInfo = '';
        $giType = \Inventory\Model\Constants::getGoodsIssueType($entity->getMovementType(), $nmtPlugin->getTranslator());
        if ($giType !== null) {
            $movementTypeInfo = $giType['type_description'];
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'issueType' => $issueType,
            'movementTypeInfo' => $movementTypeInfo,
            'nmtPlugin' => $nmtPlugin
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function add1Action()
    {
        $request = $this->getRequest();

        $this->layout("layout/user/ajax");

        if (isset($_POST['submited_form'])) {

            $redirectUrl = null;
            $pr_row_id = null;
            $target_id = null;
            $vendor_id = null;
            $currency_id = null;
            $quantity = null;
            $target_wh_id = null;
            $trx_date = null;
            $isDraft = null;
            $isActive = null;
            $isPreferredVendor = null;
            $conversionFactor = null;
            $vendorItemCode = null;
            $vendorItemUnit = null;
            $vendorUnitPrice = null;
            $leadTime = null;
            $remarks = null;

            $errors = array();
            $a_json_final = array();

            $submited_form = json_decode($_POST['submited_form'], true);
            $a_json_final['field_number'] = count($submited_form);

            foreach ($submited_form as $f) {

                switch ($f['name']) {
                    case 'pr_row_id':
                        $pr_row_id = (int) $f['value'];
                        break;
                    case 'target_id':
                        $target_id = (int) $f['value'];
                        break;
                    case 'vendor_id':
                        $vendor_id = (int) $f['value'];
                        break;
                    case 'currency_id':
                        $currency_id = $f['value'];
                        break;
                    case 'quantity':
                        $quantity = $f['value'];
                        break;
                    case 'target_wh_id':
                        $target_wh_id = (int) $f['value'];
                        break;
                    case 'trx_date':
                        $trx_date = $f['value'];
                        break;
                    case 'isDraft':
                        $isDraft = (int) $f['value'];
                        break;
                    case 'isActive':
                        $isActive = (int) $f['value'];
                        break;
                    case 'isPreferredVendor':
                        $isPreferredVendor = (int) $f['value'];
                        break;
                    case 'conversionFactor':
                        $conversionFactor = $f['value'];
                        break;
                    case 'vendorItemCode':
                        $vendorItemCode = $f['value'];
                        break;
                    case 'vendorItemUnit':
                        $vendorItemUnit = $f['value'];
                        break;
                    case 'vendorUnitPrice':
                        $vendorUnitPrice = $f['value'];
                        break;
                    case 'leadTime':
                        $leadTime = $f['value'];
                        break;
                    case 'remarks':
                        $remarks = $f['value'];
                        break;
                    case 'redirectUrl':
                        $redirectUrl = $f['value'];
                        break;
                }
            }

            $a_json_final['redirect_url'] = $redirectUrl;

            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
            if ($pr_row !== null) {
                $target = $pr_row->getItem();
            } else {
                $criteria = array(
                    'id' => $target_id
                );
                $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            }

            if ($target == null) {
                $errors[] = 'Item or PR Row object can\'t be empty. Or token key is not valid!';
            } else {
                if ($isDraft !== 1) {
                    $isDraft = 0;
                }

                if ($isActive !== 1) {
                    $isActive = 0;
                }

                if ($isPreferredVendor !== 1) {
                    $isPreferredVendor = 0;
                }
                // Inventory Transaction:
                $entity = new NmtInventoryTrx();

                $entity->setFlow('IN');
                $entity->setItem($target);

                if ($pr_row !== null) {
                    $entity->setPrRow($pr_row);
                }

                $validator = new Date();

                if (! $validator->isValid($trx_date)) {
                    $errors[] = 'Transaction date is not correct or empty!';
                    $entity->setTrxDate(null);
                } else {

                    // check if posting period is close
                    /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                    $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $p->getPostingPeriod(new \DateTime($trx_date));

                    if ($postingPeriod->getPeriodStatus() !== "C") {
                        $entity->setTrxDate(new \DateTime($trx_date));
                    } else {
                        $errors[] = 'Posting period "' . $postingPeriod->getPeriodName() . '" is closed or not created yet!';
                    }
                }

                $wh = $this->doctrineEM->find('Application\Entity\NmtInventoryWarehouse', $target_wh_id);
                if ($wh == null) {
                    $errors[] = 'Warehouse can\'t be empty. Please select a Warehouse!';
                } else {
                    $entity->setWH($wh);
                }

                if ($quantity == null) {
                    $errors[] = 'Please  enter quantity!';
                } else {

                    if (! is_numeric($quantity)) {
                        $errors[] = 'Quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'Quantity must be greater than 0!';
                        }
                        $entity->setQuantity($quantity);
                    }
                }

                $entity->setIsDraft($isDraft);
                $entity->setIsActive($isActive);

                $entity->setIsPreferredVendor($isPreferredVendor);

                $vendor = $this->doctrineEM->find('Application\Entity\NmtBpVendor', $vendor_id);

                if ($vendor == null) {
                    // $errors [] = 'Vendor can\'t be empty. Please select a vendor!';
                } else {
                    $entity->setVendor($vendor);
                }

                if ($vendorItemUnit == null) {
                    // $errors [] = 'Please enter unit of purchase';
                } else {
                    $entity->setVendorItemUnit($vendorItemUnit);
                }

                if ($conversionFactor == null) {
                    // $errors [] = 'Please enter conversion factor';
                } else {

                    if (! is_numeric($conversionFactor)) {
                        $errors[] = 'converstion_factor must be a number.';
                    } else {
                        if ($conversionFactor <= 0) {
                            $errors[] = 'converstion_factor must be greater than 0!';
                        }
                        $entity->setConversionFactor($conversionFactor);
                    }
                }

                if ($vendorUnitPrice !== null) {
                    if (! is_numeric($vendorUnitPrice)) {
                        // $errors [] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($vendorUnitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setVendorUnitPrice($vendorUnitPrice);
                    }
                }

                $currency = $this->doctrineEM->find('Application\Entity\NmtApplicationCurrency', $currency_id);
                if ($currency == null) {
                    // $errors [] = 'Curency can\'t be empty. Please select a currency!';
                } else {
                    $entity->setCurrency($currency);
                }

                $entity->setVendorItemCode($vendorItemCode);

                $entity->setLeadTime($leadTime);
                // $entity->setPmtTerm();
                $entity->setRemarks($remarks);

                if (count($errors) > 0) {
                    $a_json_final['status'] = - 1;
                    $a_json_final['errors'] = $errors;
                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                    $response->setContent(json_encode($a_json_final));
                    return $response;
                }
                ;
                // OK now
                $a_json_final['status'] = 1;
                $a_json_final['errors'] = $errors;

                $entity->setConversionText($entity->getVendorItemUnit() . ' = ' . $entity->getConversionFactor() . '*' . $target->getStandardUom()
                    ->getUomCode());

                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));

                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $new_entity_id = $entity->getId();

                $entity->setChecksum(md5($new_entity_id . uniqid(microtime())));
                $this->doctrineEM->flush();

                $this->flashMessenger()->addMessage($quantity . ' of Item "' . $target->getItemName() . '" has been received successfully!');
                // return $this->redirect()->toUrl($redirectUrl);

                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($a_json_final));
                return $response;
            }
        }

        // NO POST
        $redirectUrl = Null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $target_id = (int) $this->params()->fromQuery('target_id');
        $pr_row_id = (int) $this->params()->fromQuery('pr_row_id');

        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');

        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $criteria1 = array(
            'id' => $pr_row_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = new NmtInventoryTrx();

        if ($pr_row_id > 0) {
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria1);
            $entity->setPrRow($pr_row);
        }

        $target = null;
        if ($target_id > 0) {
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            $entity->setItem($target);
        }

        // set null
        $entity->setTrxDate(null);
        $entity->setIsActive(1);
        $entity->setIsPreferredVendor(1);

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        if ($default_wh !== null) {
            $entity->setWh($default_wh);
        }

        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );

        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list
        ));
    }

    /**
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
        $checksum = $this->params()->fromQuery('checksum');

        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $criteria = array(
            'item' => $target
        );

        $sort_criteria = array(
            'trxDate' => "DESC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        /*
         * $this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
         * $this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
         */
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'target' => $target
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

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getItemSearchService()
    {
        return $this->itemSearchService;
    }

    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
        return $this;
    }
}

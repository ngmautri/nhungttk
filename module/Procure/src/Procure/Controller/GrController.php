<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Domain\Shared\DTOFactory;
use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcurePo;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Procure\Application\Command\GR\SaveCopyFromPOCmd;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandlerDecorator;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Application\Command\GR\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Service\GR\GRService;
use Procure\Domain\Shared\Constants;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 * Good Receipt Controller
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrController extends AbstractActionController
{

    protected $doctrineEM;

    protected $grService;

    protected $goodsReceiptService;

    /*
     * @return \Zend\View\Model\ViewModel
     */
    public function createFromPoAction()

    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/gr/create-from-po";
        $form_title = "Goods Receip from PO";
        $action = Constants::FORM_ACTION_GR_FROM_PO;
        $viewTemplete = "procure/gr/crudHeader";

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $source_id = (int) $this->params()->fromQuery('source_id');
            $source_token = $this->params()->fromQuery('source_token');

            $options = new CopyFromPOOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);
            $rootEntity = $this->getGoodsReceiptService()->createFromPO($source_id, $source_token, $options);

            // echo memory_get_usage();
            // var_dump($po->makeDTOForGrid());
            // echo memory_get_usage();

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $dto = $rootEntity->makeDetailsDTO();

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,

                'source_id' => $source_id,
                'source_token' => $source_token,
                'dto' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ===============================

        try {
            $data = $prg;

            /**@var \Application\Entity\MlaUsers $u ;*/
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();
            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $version = $data['version'];

            $dto = DTOFactory::createDTOFromArray($data, new GrDTO());
            $options = new CopyFromPOOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);

            $rootEntity = $this->getGoodsReceiptService()->createFromPO($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromPOOptions($companyId, $userId, __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromPOCmdHandler();
            $cmdHandlerDecorator = new SaveCopyFromPOCmdHandlerDecorator($cmdHandler);
            $cmd = new SaveCopyFromPOCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'source_id' => $source_id,
                'source_token' => $source_token,
                'dto' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/gr/view?entity_id=%s&token=%s", $entity_id, $entity_token);

        // $this->flashMessenger()->addMessage($notification->successMessage(false));
        $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
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
            $entity_id = (int) $data['entity_id'];
            $entity_token = $data['entity_token'];

            $reversalDate = $data['reversalDate'];
            $reversalReason = $data['reversalReason'];

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $entity_array = $res->getGr($entity_id, $entity_token);

            if ($entity_array == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $entity = null;
            if ($entity_array[0] instanceof NmtProcureGr) {
                $entity = $entity_array[0];
            }

            if ($entity == null) {
                $m = $nmtPlugin->translate("Reversal failed!");
                $this->flashMessenger()->addMessage($m);

                $errors[] = "not found!";
                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'total_row' => $entity_array['total_row'],
                    'active_row' => $entity_array['active_row'],
                    'max_row_number' => $entity_array['total_row'],
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $errors = $this->grService->reverse($entity, $u, $reversalDate, $reversalReason, __METHOD__);

            if (count($errors) > 0) {

                $m = $nmtPlugin->translate("Reversal failed!");
                $this->flashMessenger()->addMessage($m);

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'total_row' => $entity_array['total_row'],
                    'active_row' => $entity_array['active_row'],
                    'max_row_number' => $entity_array['total_row'],
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/gr/reverse");
                return $viewModel;
            }

            $m = sprintf("GR #%s reversed", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/gr/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $entity_array = $res->getGr($id, $token);

        if ($entity_array == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($entity_array[0] instanceof NmtProcureGr) {
            $entity = $entity_array[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'currency_list' => $currency_list,
            'total_row' => $entity_array['total_row'],
            'active_row' => $entity_array['active_row'],
            'max_row_number' => $entity_array['total_row'],
            'nmtPlugin' => $nmtPlugin
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
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);

        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($gr[0] instanceof NmtProcureGr) {
            $entity = $gr[0];
        }

        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {

            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        if (! $entity == null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row'],
                'active_row' => $gr['active_row'],
                'max_row_number' => $gr['total_row'],
                'nmtPlugin' => $nmtPlugin
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Make GR from PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $id = (int) $request->getPost('source_id');
            $token = $request->getPost('source_token');

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($id, $token);

            /**@var \Application\Entity\NmtProcurePo $source ;*/
            $source = null;

            if ($po !== null) {
                if ($po[0] instanceof NmtProcurePo) {
                    $source = $po[0];
                }
            }

            if (! $source instanceof \Application\Entity\NmtProcurePo) {
                $errors[] = 'PO can\'t be empty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'source' => null,
                    'currency_list' => $currency_list
                ));
            }

            $currentState = $request->getPost('currentState');
            $warehouse_id = (int) $request->getPost('target_wh_id');

            $grDate = $request->getPost('grDate');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');

            if ($isActive !== 1) {
                $isActive = 0;
            }

            $entity = new NmtProcureGr();

            $entity->setContractNo($source->getContractNo());
            $entity->setContractDate($source->getContractDate());

            // unchangeable.
            // $entity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_GR);

            $entity->setIsActive($isActive);
            $entity->setCurrentState($currentState);

            if ($source->getVendor() instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($source->getVendor());
                $entity->setVendorName($source->getVendor()
                    ->getVendorName());
            } else {
                $errors[] = $nmtPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
            }

            if ($source->getCurrency() instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($source->getCurrency());
                $entity->setCurrencyIso3($source->getCurrency()
                    ->getCurrency());
            } else {
                $errors[] = $nmtPlugin->translate('Currency can\'t be empty. Please select a currency!');
            }

            $validator = new Date();

            // check one more time when posted.
            if ($grDate !== null) {
                if (! $validator->isValid($grDate)) {
                    $errors[] = $nmtPlugin->translate('Goods receipt Date is not correct or empty!');
                } else {

                    // check if posting period is close
                    /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                    $res = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $res->getPostingPeriod(new \DateTime($grDate));

                    if ($postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                        if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                            $errors[] = sprintf('Period "%s" is closed', $postingPeriod->getPeriodName());
                        } else {
                            $entity->setGrDate(new \DateTime($grDate));
                        }
                    } else {
                        $errors[] = sprintf('Period for GR Date "%s" is not created yet', $grDate);
                    }
                }
            }

            $warehouse = null;
            if ($warehouse_id > 0) {
                $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
            }

            if ($warehouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                $entity->setWarehouse($warehouse);
            } else {
                $errors[] = $nmtPlugin->translate('Warehouse can\'t be empty. Please select a Wahrhouse!');
            }

            $entity->setRemarks($remarks);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'source' => $source,
                    'currency_list' => $currency_list
                ));
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            // $entity->setTransactionStatus(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setIsDraft(1);
            $entity->setIsPosted(0);

            $createdOn = new \DateTime();

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $this->doctrineEM->persist($entity);

            try {
                $this->grService->copyFromPO($entity, $source, $u, true);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'source' => $source,
                    'currency_list' => $currency_list
                ));
            }

            $m = sprintf("[OK] Goods Receipt #%s created from P/O #%s", $entity->getSysNumber(), $source->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/gr/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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

        $id = (int) $this->params()->fromQuery('source_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');

        /**@var \Application\Entity\NmtProcurePo $source ;*/
        $po = $res->getPo($id, $token);

        $source = null;
        if ($po[0] instanceof NmtProcurePo) {
            $source = $po[0];
        }

        if (! $source instanceof \Application\Entity\NmtProcurePo) {
            return $this->redirect()->toRoute('access_denied');
        }

        $po_rows = $res->getPOStatus($id, $token);

        if ($po_rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        if (count($po_rows) > 0) {

            $errors = array();
            $total_open_gr = 0;
            $total_draft_gr = 0;

            foreach ($po_rows as $r) {
                $total_open_gr = $total_open_gr + $r['open_gr_qty'];
                $total_draft_gr = $total_draft_gr + $r['draft_gr_qty'];
            }

            if ($total_draft_gr > 0) {

                $redirectUrl = '/procure/gr/list';
                $m = sprintf("[INFO] There is draft GR for PO #. Pls review it!", $source->getSysNumber());
                $this->flashMessenger()->addMessage($m);

                return $this->redirect()->toUrl($redirectUrl);
            }

            if ($total_open_gr == 0) {

                $redirectUrl = sprintf('/procure/po/add1?token=%s&entity_id=%s', $source->getToken(), $source->getId());
                $m = sprintf("[INFO] Items of PO # received fully!", $source->getSysNumber());
                $this->flashMessenger()->addMessage($m);

                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        $entity = new NmtProcureGr();
        $entity->setContractNo($source->getContractNo());
        $entity->setContractDate($source->getContractDate());
        $entity->setIsActive(1);

        // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'source' => $source,
            'currency_list' => $currency_list
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function copyFromPo1Action()
    {
        $this->layout("Procure/layout-fullscreen");

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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);

        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $gr[0],
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row']
                // 'active_row' => $invoice['active_row'],
                // 'max_row_number' => $invoice['total_row'],
                // 'net_amount' => $invoice['net_amount'],
                // 'tax_amount' => $invoice['tax_amount'],
                // 'gross_amount' => $invoice['gross_amount']
            ));
        }
        // return $this->redirect()->toRoute('access_denied');
    }

    /**
     * Review and Post GR.
     * Document can't be changed.
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $request = $this->getRequest();

        $this->layout("Procure/layout-fullscreen");

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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $id = (int) $data['entity_id'];
            $token = $data['token'];

            $entity_array = $res->getGr($id, $token);

            if ($entity_array == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            if (! $entity_array[0] instanceof \Application\Entity\NmtProcureGr) {
                $errors[] = $nmtPlugin->translate('GR object is not found!');

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => null,
                        'nmtPlugin' => $nmtPlugin,

                        'total_row' => null,
                        'active_row' => null,
                        'max_row_number' => null
                    ));
                }
            }

            /**@var \Application\Entity\NmtProcureGr $entity ;*/
            $entity = $entity_array[0];

            if ($entity->getLocalCurrency() == null) {
                $entity->setLocalCurrency($default_cur);
            }

            $errors = $this->grService->postGR($entity, $data, $u, true, __METHOD__);

            if (count($errors) > 0) {

                $errors[] = $data['grDate'];

                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin,
                    'total_row' => $entity_array['total_row'],
                    'active_row' => $entity_array['active_row'],
                    'max_row_number' => $entity_array['total_row']
                ));
            }

            $m = sprintf('[OK] GR #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/procure/gr/list";
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

        $entity_array = $res->getGr($id, $token);

        $entity = null;
        if ($entity_array[0] instanceof NmtProcureGr) {
            $entity = $entity_array[0];
        }

        if (! $entity instanceof NmtProcureGr) {
            return $this->redirect()->toRoute('access_denied');
        }

        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            $m = sprintf('GR #%s - %s already posted!', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/procure/gr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        if ($entity_array['active_row'] == 0) {
            $m = sprintf('[INFO] GR #%s has no lines.', $entity->getSysNumber());
            $m1 = $nmtPlugin->translate('Document is incomplete!');
            $this->flashMessenger()->addMessage($m1);
            $redirectUrl = "/procure/gr-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'nmtPlugin' => $nmtPlugin,

            'total_row' => $entity_array['total_row'],
            'active_row' => $entity_array['active_row'],
            'max_row_number' => $entity_array['total_row']
        ));
    }

    /**
     * Adding new GR
     * Good Receipt First, Invoice Late
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");

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

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new NmtProcureGr();
            $entity->setLocalCurrency($default_cur);
            $entity->setIsDraft(1);
            $entity->setIsPosted(0);

            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);

            $errors = $this->grService->saveHeader($entity, $data, $u, TRUE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/gr/crud");
                return $viewModel;
            }

            $m = sprintf("[OK] Good Receipts: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/gr-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
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

        $entity = new NmtProcureGr();
        $entity->setIsActive(1);

        // Default currency
        if ($default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
            $entity->setCurrency($default_cur);
        }

        // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/gr/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function add1Action()
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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }

        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $po['total_row'],
                'active_row' => $po['active_row'],
                'max_row_number' => $po['total_row'],
                'net_amount' => $po['net_amount'],
                'tax_amount' => $po['tax_amount'],
                'gross_amount' => $po['gross_amount']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function add2Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }

        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $po['total_row'],
                'active_row' => $po['active_row'],
                'max_row_number' => $po['total_row'],
                'net_amount' => $po['net_amount'],
                'tax_amount' => $po['tax_amount'],
                'gross_amount' => $po['gross_amount']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity_id = $data['entity_id'];
            $token = $data['token'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcureGr $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGr')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Good Receipt not found!';
                $this->flashMessenger()->addMessage('Something wrong!');
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => null,
                    'entity' => null,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/gr/crud");
                return $viewModel;
            }

            // ====== VALIDATED ====== //

            $default_cur = null;
            if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
                $default_cur = $u->getCompany()->getDefaultCurrency();
            }

            $entity->setLocalCurrency($default_cur);
            $errors = $this->grService->saveHeader($entity, $data, $u, false, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/gr/crud");
                return $viewModel;
            }

            $m = sprintf("[OK] Good Receipts: %s update!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/gr/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST ====================
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

        /**@var \Application\Entity\NmtProcureGr $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGr')->findOneBy($criteria);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/gr/crud");
        return $viewModel;
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

        if ($sort_by == null) :
            $sort_by = "sysNumber";
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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $list = $res->getGrList($is_active, $currentState, $docStatus, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getGrList($is_active, $currentState, $docStatus, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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
    public function listOfPOAction()
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
            $sort_by = "createdOn";
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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $list = $res->getGrList($is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getGrList($is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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
            $sort_by = "createdOn";
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
     * @return \Procure\Service\GrService
     */
    public function getGrService()
    {
        return $this->grService;
    }

    /**
     *
     * @param \Procure\Service\GrService $grService
     */
    public function setGrService(\Procure\Service\GrService $grService)
    {
        $this->grService = $grService;
    }

    /**
     *
     * @return \Procure\Application\Service\GR\GRService
     */
    public function getGoodsReceiptService()
    {
        return $this->goodsReceiptService;
    }

    /**
     *
     * @param GRService $goodsReceiptService
     */
    public function setGoodsReceiptService(GRService $goodsReceiptService)
    {
        $this->goodsReceiptService = $goodsReceiptService;
    }
}

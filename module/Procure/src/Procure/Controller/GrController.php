<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Domain\Shared\DTOFactory;
use Application\Entity\NmtProcureGr;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\GR\SaveCopyFromPOCmd;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Application\Command\GR\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Reporting\GR\GrReporter;
use Procure\Application\Service\GR\GRService;
use Procure\Domain\Shared\Constants;
use Psr\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;
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

    protected $grReporter;

    protected $goodsReceiptService;

    protected $logger;

    /*
     * @return \Zend\View\Model\ViewModel
     */
    public function createFromPoAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/gr/create-from-po";
        $form_title = "Goods Receip from PO";
        $action = Constants::FORM_ACTION_GR_FROM_PO;
        $viewTemplete = "procure/gr/crudHeader";

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
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
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
        $file_type = $this->params()->fromQuery('file_type');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }

        if ($docStatus == null) {
            $docStatus = "posted";
        }

        if ($sort_by == null) {
            $sort_by = "sysNumber";
            $sort_by = "sysNumber";
        }

        if ($sort_by == null) {
            $sort_by = "createdOn";
        }

        if ($sort_by == null) {
            $sort_by = "createdOn";
        }

        $current_state = null;
        $filter_by = null;
        $paginator = null;
        $limit = null;
        $offset = null;

        /**
         *
         * @todo: CACHE
         */
        $total_records = $this->getGrReporter()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getGrReporter()->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset, $file_type);

        $viewModel = new ViewModel(array(
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

        $viewModel->setTemplate("procure/gr/dto_list");
        return $viewModel;
    }

    // =====================================
    // Setter and Getter.
    // =====================================

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

    /**
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return \Procure\Application\Reporting\GR\GrReporter
     */
    public function getGrReporter()
    {
        return $this->grReporter;
    }

    /**
     *
     * @param GrReporter $grReporter
     */
    public function setGrReporter(GrReporter $grReporter)
    {
        $this->grReporter = $grReporter;
    }
}

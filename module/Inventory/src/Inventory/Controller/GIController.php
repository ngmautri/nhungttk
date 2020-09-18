<?php
namespace Inventory\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Util\FileExtension;
use Application\Domain\Util\JsonErrors;
use Application\Entity\NmtInventoryMv;
use Application\Entity\NmtInventoryTrx;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Transaction\CreateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\CreateRowCmdHandler;
use Inventory\Application\Command\Transaction\PostCmdHandler;
use Inventory\Application\Command\Transaction\UpdateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\UpdateRowCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxCreateOptions;
use Inventory\Application\Command\Transaction\Options\TrxPostOptions;
use Inventory\Application\Command\Transaction\Options\TrxRowCreateOptions;
use Inventory\Application\Command\Transaction\Options\TrxRowUpdateOptions;
use Inventory\Application\Command\Transaction\Options\TrxUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Application\DTO\Transaction\TrxRowDTO;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Service\Transaction\TrxService;
use Inventory\Application\Service\Upload\Transaction\TrxRowsUpload;
use Inventory\Application\Service\Upload\Transaction\UploadFactory;
use Inventory\Domain\Transaction\Contracts\TrxType;
use MLA\Paginator;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Exception;
use Inventory\Application\Command\Transaction\Options\CreateTrxFromGRFromPurchasingOptions;
use Inventory\Application\Command\Transaction\CreateGIForReturnPOCmdHandler;

/**
 * Goods Issue
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIController extends AbstractGenericController
{

    const BASE_URL = '/inventory/gi/%s';

    const VIEW_URL = '/inventory/gi/view?entity_id=%s&entity_token=%s';

    const REVIEW_URL = '/inventory/gi/review?entity_id=%s&entity_token=%s';

    const ADD_ROW_URL = '/inventory/gi/add-row?target_id=%s&target_token=%s';

    protected $trxService;

    protected $trxUploadService;

    public function createReturnAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var TrxDTO $dto ;*/
        $this->layout("Inventory/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/inventory/gi/create-return";
        $form_title = "Goods return from PO";
        $action = Constants::FORM_ACTION_WH_GI_FOR_PO;
        $viewTemplete = "inventory/gi/crudHeader";

        $prg = $this->prg($form_action, true);
        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $errors = null;
            $dto = null;
            $version = null;

            try {
                $source_id = (int) $this->params()->fromQuery('target_id');
                $source_token = $this->params()->fromQuery('target_token');
                $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($source_id, $source_token);

                if ($rootEntity == null) {
                    return $this->redirect()->toRoute('not_found');
                }

                $dto = $rootEntity->makeDetailsDTO();
                $dto->movementType = TrxType::GI_FOR_RETURN_PO;
                $version = $dto->getRevisionNo();
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            $viewModel = new ViewModel(array(
                'errors' => $errors,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $source_id,
                'target_token' => $source_token,
                'headerDTO' => $dto,
                'version' => $version,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'transactionType' => $transactionType
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ===============================

        $notification = new Notification();

        try {
            $data = $prg;

            $source_id = $data['target_id'];
            $source_token = $data['target_id'];

            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($source_id, $source_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $dto = DTOFactory::createDTOFromArray($data, new TrxDTO());
            $options = new CreateTrxFromGRFromPurchasingOptions($this->getCompanyId(), $this->getUserId(), __METHOD__, $rootEntity);
            $cmdHandler = new CreateGIForReturnPOCmdHandler();

            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();

            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $source_id,
                'target_token' => $source_token,
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'transactionType' => $transactionType
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/inventory/gi/view?entity_token=%s&entity_id=%s", $dto->getToken(), $dto->getId());
        $this->flashMessenger()->addMessage($notification->successMessage(true));

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function uploadRowsAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();

        $form_action = \sprintf(self::BASE_URL, 'upload-rows');
        $form_title = "Upload";
        $action = Constants::FORM_ACTION_UPLOAD;
        $viewTemplete = \sprintf(self::BASE_URL, 'review-v1');

        $transactionType = TrxType::getGoodIssueTrx();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $notify = new Notification();

            try {

                $entity_id = $request->getPost('entity_id');
                $entity_token = $request->getPost('entity_token');

                $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

                if ($rootEntity == null) {
                    return $this->redirect()->toRoute('not_found');
                }

                $file_name = null;
                $file_size = null;
                $file_tmp = null;
                $file_ext = null;
                $file_type = null;

                if (isset($_FILES['uploaded_file'])) {
                    $file_name = $_FILES['uploaded_file']['name'];
                    $file_size = $_FILES['uploaded_file']['size'];
                    $file_tmp = $_FILES['uploaded_file']['tmp_name'];
                    $file_type = $_FILES['uploaded_file']['type'];
                    $file_ext1 = (explode('.', $file_name));
                    $file_ext = end($file_ext1);
                }

                $ext = FileExtension::get($file_type);
                if ($ext == null) {
                    $ext = \strtolower($file_ext);
                }
                $expensions = array(
                    "xlsx",
                    "xls",
                    "csv"
                );

                if (in_array($ext, $expensions) == false) {
                    $notify->addError("File not supported or empty! " . $file_name);
                }

                $dto = $rootEntity->makeSnapshot();

                if ($notify->hasErrors()) {
                    $viewModel = new ViewModel(array(
                        'errors' => $notify->getErrors(),
                        'redirectUrl' => null,
                        'entity_id' => $entity_id,
                        'entity_token' => $entity_token,
                        'headerDTO' => $dto,
                        'version' => $rootEntity->getRevisionNo(),
                        'nmtPlugin' => $nmtPlugin,
                        'form_action' => $form_action,
                        'form_title' => $form_title,
                        'action' => $action,
                        'transactionType' => $transactionType,
                        'rowOutput' => $rootEntity->getRowsOutput()
                    ));
                    $viewModel->setTemplate($viewTemplete);
                    return $viewModel;
                }

                $folder = ROOT . "/temp";

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true); // important
                }

                move_uploaded_file($file_tmp, "$folder/$file_name");

                $uploader = UploadFactory::create($rootEntity->getMovementType());
                $uploader->setDoctrineEM($this->getDoctrineEM());
                $uploader->setLogger($this->getLogger());
                $uploader->doUploading($rootEntity, "$folder/$file_name");
            } catch (Exception $e) {
                $this->logException($e, false);
                $notify->addError($e->getMessage());

                $viewModel = new ViewModel(array(
                    'errors' => $notify->getErrors(),
                    'redirectUrl' => null,
                    'entity_id' => $entity_id,
                    'entity_token' => $entity_token,
                    'headerDTO' => $dto,
                    'version' => $rootEntity->getRevisionNo(),
                    'nmtPlugin' => $nmtPlugin,
                    'form_action' => $form_action,
                    'form_title' => $form_title,
                    'action' => $action,
                    'transactionType' => $transactionType,
                    'rowOutput' => $rootEntity->getRowsOutput()
                ));
                $viewModel->setTemplate($viewTemplete);
                return $viewModel;
            }

            $this->logInfo(\sprintf('Trx rows for #%s uploaded!, (%s-%s)', $rootEntity->getId(), $file_name, $file_size));
            $this->flashMessenger()->addMessage($notify->successMessage(false));
            $redirectUrl = sprintf(self::REVIEW_URL, $entity_id, $entity_token);
            \unlink("$folder/$file_name");
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO Posting
        // =========================

        $entity_id = (int) $this->params()->fromQuery('target_id');
        $entity_token = $this->params()->fromQuery('target_token');
        $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $dto = $rootEntity->makeSnapshot();

        $viewModel = new ViewModel(array(
            'errors' => null,
            'redirectUrl' => null,
            'entity_id' => $entity_id,
            'entity_token' => $entity_token,
            'headerDTO' => $dto,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'action' => $action,
            'transactionType' => $transactionType,
            'rowOutput' => $rootEntity->getRowsOutput()
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $this->layout("Inventory/layout-fullscreen");
        $form_action = null;
        $form_title = $nmtPlugin->translate("Show Transaction");
        $action = Constants::FORM_ACTION_SHOW;
        $viewTemplete = "inventory/gi/review-v1";
        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        /**@var \Application\Entity\MlaUsers $u ;*/

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($id, $token);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $movementType = $rootEntity->getMovementType();
        switch ($movementType) {
            case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                $f = '/inventory/transfer-wh/view?entity_id=%s&entity_token=%s';
                $redirectUrl = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());
                return $this->redirect()->toUrl($redirectUrl);
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(new TrxDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'transactionType' => $transactionType
        ));
        $viewModel->setTemplate($viewTemplete);
        $this->getLogger()->info(\sprintf("Trx #%s viewed by #%s", $id, $this->getUserId()));
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function rowGirdAction()
    {
        try {
            if (isset($_GET["pq_curpage"])) {
                $pq_curPage = $_GET["pq_curpage"];
            } else {
                $pq_curPage = 1;
            }

            if (isset($_GET["pq_rpp"])) {
                $pq_rPP = $_GET["pq_rpp"];
            } else {
                $pq_rPP = 100;
            }

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $total_records = $this->getTrxService()->getTotalRows($entity_id, $entity_token);

            $a_json_final = [];
            $a_json_final['totalRecords'] = $total_records;
            $a_json_final['curPage'] = $pq_curPage;

            // $total_records = 873;
            $outputStrategy = SaveAsSupportedType::OUTPUT_IN_ARRAY;
            $limit = null;
            $offset = null;

            if ($total_records > 0) {

                if ($total_records > $pq_rPP) {
                    $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                    $offset = $paginator->minInPage - 1;
                    $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                }
            }
            $rootEntity = $this->getTrxService()->getLazyDocOutputByTokenId($entity_id, $entity_token, $offset, $limit, $outputStrategy);

            $a_json_final['data'] = $rootEntity->getRowsOutput();

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($a_json_final));
            $this->logInfo(\sprintf('Json Last error: %s', JsonErrors::getErrorMessage(json_last_error())));

            return $response;
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {
        $this->layout("Inventory/layout-fullscreen");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $transactionType = TrxType::getGoodIssueTrx();

        $form_action = "/inventory/gi/view";
        $form_title = "Invoice Invoice:";
        $action = null;
        $viewTemplete = "inventory/gi/review-v1";

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->getLogger()->info(\sprintf("Trx #%s saved as format %s by #%s", $id, $file_type, $this->getUserId()));

        $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($id, $token, $file_type);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(new TrxDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'transactionType' => $transactionType
        ));
        $viewModel->setTemplate($viewTemplete);

        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function createAction()
    {
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $isAllowed = true;

        $viewTemplete = "inventory/gi/crudHeader";
        $action = Constants::FORM_ACTION_ADD;
        $form_action = "/inventory/gi/create";

        $form_title = $nmtPlugin->translate("Create Good Issue");

        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        $prg = $this->prg('/inventory/gi/create', true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $redirectUrl = null;

            $viewModel = new ViewModel(array(

                'action' => $action,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => null,
                'nmtPlugin' => $nmtPlugin,
                'transactionType' => $transactionType,
                'isAllowed' => $isAllowed,
                'errors' => null,
                'localCurrencyId' => $this->getLocalCurrencyId()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // Is Posting
        // ++++++++++++++++++++++++++++++

        $notification = new Notification();
        try {
            $data = $prg;

            $movementType = $data['movementType'];
            switch ($movementType) {
                case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                    $f = '/inventory/transfer-wh/create?sourceWH=%s&movementDate=%s';
                    $redirectUrl = sprintf($f, $data['warehouse'], $data['movementDate']);
                    return $this->redirect()->toUrl($redirectUrl);
            }

            /**
             *
             * @var TrxDTO $dto ;
             */
            $dto = DTOFactory::createDTOFromArray($data, new TrxDTO());

            $options = new TrxCreateOptions($this->getCompanyId(), $this->getLocalCurrencyId(), $this->getUserId(), __METHOD__);
            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();

            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'action' => $action,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'transactionType' => $transactionType,
                'errors' => $notification->getErrors(),
                'localCurrencyId' => $this->getLocalCurrencyId()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/inventory/gi/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/inventory/gi/update";
        $form_title = "Edit Good Receipt";
        $action = Constants::FORM_ACTION_EDIT;
        $viewTemplete = "inventory/gi/crudHeader";

        $userId = $this->getUserId();
        $localCurrencyId = $this->getLocalCurrencyId();
        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $movementType = $rootEntity->getMovementType();
            switch ($movementType) {
                case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                    $f = '/inventory/transfer-wh/update?entity_id=%s&entity_token=%s';
                    $redirectUrl = sprintf($f, $rootEntity->getid(), $rootEntity->getToken());
                    return $this->redirect()->toUrl($redirectUrl);
            }

            $dto = $rootEntity->makeSnapshot();

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $localCurrencyId,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {
            // POSTING
            $data = $prg;
            $dto = DTOFactory::createDTOFromArray($data, new TrxDTO());
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new TrxUpdateOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            // echo $e->getTraceAsString();
            $this->getLogger()->alert($e->getTraceAsString());
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $dto,
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        // $redirectUrl = sprintf("/inventory/transaction/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        $redirectUrl = sprintf("/inventory/gi/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addRowAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var TrxRowDTO $dto ;
         */
        $this->layout("Inventory/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/inventory/gi/add-row";
        $form_title = "Add Trx Row";
        $action = Constants::FORM_ACTION_ADD;
        $viewTemplete = "inventory/gi/crudRow";
        $userId = $this->getUserId();

        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($target_id, $target_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $this->getLogger()->info(\sprintf("Row Trx #%s is going to be created by %s", $target_id, $userId));

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'dto' => null,
                'headerDTO' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete . $rootEntity->getMovementType());
            return $viewModel;
        }

        // POSTING
        // =====================

        try {

            $data = $prg;

            $dto = DTOFactory::createDTOFromArray($data, new TrxRowDTO());

            // var_dump($dto);
            $rootEntityId = $data['target_id'];
            $rootEntityToken = $data['target_token'];
            $version = $data['version'];
            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($rootEntityId, $rootEntityToken);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new TrxRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHander = new CreateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHander);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
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
                'target_id' => $rootEntityId,
                'target_token' => $rootEntityToken,
                'dto' => $dto,
                'headerDTO' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $this->getLogger()->info(\sprintf("Row Trx #%s is not created by %s. Error: %s", $rootEntityId, $this->getUserId(), $notification->errorMessage()));

            $viewModel->setTemplate($viewTemplete . $rootEntity->getMovementType());
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/inventory/gi/add-row?target_id=%s&target_token=%s", $rootEntityId, $rootEntityToken);

        $this->getLogger()->info(\sprintf("Row Trx #%s is created by %s", $rootEntityId, $userId));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateRowAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var TrxRowDTO $dto ;
         */
        $this->layout("Inventory/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/inventory/gi/update-row";
        $form_title = "Update Good Receipt Row";
        $action = Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/inventory/gi/crudRow";
        $userId = $this->getUserId();

        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $result = $this->getTrxService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;
            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }
            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }
            if (! $rootDTO instanceof TrxDTO || ! $localDTO instanceof TrxRowDTO) {
                return $this->redirect()->toRoute('not_found');
            }
            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $rootDTO->getRevisionNo(),
                'headerDTO' => $rootDTO,
                'dto' => $localDTO, // row
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete . $rootDTO->getMovementType());
            return $viewModel;
        }
        // Posting
        // =============================
        $data = $prg;

        $dto = DTOFactory::createDTOFromArray($data, new TrxRowDTO());
        $userId = $this->getUserId();

        $target_id = $data['target_id'];
        $target_token = $data['target_token'];
        $entity_id = $data['entity_id'];
        $entity_token = $data['entity_token'];
        $version = $data['version'];

        $result = $this->getTrxService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);
        $rootEntity = null;
        $localEntity = null;
        $rootDTO = null;
        $localDTO = null;

        if (isset($result["rootEntity"])) {
            $rootEntity = $result["rootEntity"];
        }
        if (isset($result["localEntity"])) {
            $localEntity = $result["localEntity"];
        }
        if (isset($result["rootDTO"])) {
            $rootDTO = $result["rootDTO"];
        }
        if (isset($result["localDTO"])) {
            $localDTO = $result["localDTO"];
        }
        if ($rootEntity == null || $localEntity == null || $rootDTO == null || $localDTO == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $notification = new Notification();

        try {

            $options = new TrxRowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }
        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $dto,
                'headerDTO' => $rootDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete . $rootDTO->getMovementType());
            $this->getLogger()->error(\sprintf("Row Trx #%s is not updated by %s. Error: %s", $target_id, $userId, $notification->errorMessage()));

            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/inventory/gi/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     * Review and Post GR.
     * Document can't be changed.
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/inventory/gi/review";
        $form_title = "Review Transaction";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "inventory/gi/review-v1";

        $transactionType = TrxType::getGoodIssueTrx($nmtPlugin->getTranslator());

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(new TrxDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        // POSTING
        // ====================================

        try {
            $notification = new Notification();

            $data = $prg;
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new TrxDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                $this->flashMessenger()->addMessage(\sprintf("%s-%s", $entity_id, $entity_token));
                return $this->redirect()->toRoute('not_found');
            }
            $options = new TrxPostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $notification = $dto->getNotification();
            $msg = sprintf("Trx #%s is posted", $entity_id);
            // $redirectUrl = sprintf("/procure/ap/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $redirectUrl = "/inventory/item-transaction/list";
            http: // localhost:81/procure/ap-report/header-status
        } catch (\Exception $e) {
            $msg = sprintf("%s", $e->getMessage());
            $redirectUrl = sprintf("/inventory/gi/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(new TrxDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'transactionType' => $transactionType
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function reverseAction()
    {}

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
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

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
            return $this->redirect()->toRoute('not_found');
        }

        $entity = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $entity = $gi[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('not_found');
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
            'currency_list' => $currency_list,
            'issueType' => $issueType,
            'movementTypeInfo' => $movementTypeInfo
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {}

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {}

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function review1Action()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/gi-create-layout");

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

            /**
             *
             * @var Notification $notification
             */
            $notification = $this->transactionService->post($entity_id, $entity_token, __METHOD__);

            if ($notification->hasErrors()) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $notification->getErrors(),
                    'entity' => $entity,
                    'issueType' => $issueType,
                    'movementTypeInfo' => $movementTypeInfo,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $m = sprintf("[OK] Goods Movement: %s posted!");
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
            return $this->redirect()->toRoute('not_found');
        }

        $entity = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $entity = $gi[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('not_found');
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

    // /=======================================
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
     * @return \Inventory\Application\Service\Transaction\TrxService
     */
    public function getTrxService()
    {
        return $this->trxService;
    }

    /**
     *
     * @param TrxService $trxService
     */
    public function setTrxService(TrxService $trxService)
    {
        $this->trxService = $trxService;
    }

    /**
     *
     * @return \Inventory\Application\Service\Upload\Transaction\TrxRowsUpload
     */
    public function getTrxUploadService()
    {
        return $this->trxUploadService;
    }

    /**
     *
     * @param TrxRowsUpload $trxUploadService
     */
    public function setTrxUploadService(TrxRowsUpload $trxUploadService)
    {
        $this->trxUploadService = $trxUploadService;
    }
}

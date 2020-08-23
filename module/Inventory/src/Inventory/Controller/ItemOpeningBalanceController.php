<?php
namespace Inventory\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Util\FileExtension;
use Application\Domain\Util\JsonErrors;
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
use Inventory\Domain\Transaction\Contracts\TrxType;
use MLA\Paginator;
use Zend\View\Model\ViewModel;
use Exception;

/**
 * Opening Balance
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemOpeningBalanceController extends AbstractGenericController
{

    protected $trxService;

    protected $trxUploadService;

    const BASE_URL = '/inventory/item-opening-balance/%s';

    const VIEW_URL = '/inventory/item-opening-balance/view?entity_id=%s&entity_token=%s';

    const REVIEW_URL = '/inventory/item-opening-balance/review?entity_id=%s&entity_token=%s';

    const ADD_ROW_URL = '/inventory/item-opening-balance/add-row?target_id=%s&target_token=%s';

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
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

        $transactionType = TrxType::getGoodReceiptTrx();

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
                        'transactionType' => $transactionType
                    ));
                    $viewModel->setTemplate($viewTemplete);
                    return $viewModel;
                }

                $folder = ROOT . "/temp";

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true); // important
                }

                move_uploaded_file($file_tmp, "$folder/$file_name");

                $this->getTrxUploadService()->doUploading($rootEntity, "$folder/$file_name");
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
                    'transactionType' => $transactionType
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
            'transactionType' => $transactionType
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
        $viewTemplete = \sprintf(self::BASE_URL, 'review-v1');
        $transactionType = TrxType::getGoodReceiptTrx();

        /**@var \Application\Entity\MlaUsers $u ;*/

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getTrxService()->getLazyDocDetailsByTokenId($id, $token);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $headerDTO = $rootEntity->makeDTOForGrid(new TrxDTO());
        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'headerDTO' => $headerDTO,
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'transactionType' => $transactionType
        ));
        $viewModel->setTemplate($viewTemplete);
        $this->logInfo(\sprintf("Trx #%s viewed by #%s", $id, $this->getUserId()));
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
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $isAllowed = true;

        $viewTemplete = \sprintf(self::BASE_URL, 'crudHeader');

        $action = Constants::FORM_ACTION_ADD;
        $form_action = \sprintf(self::BASE_URL, 'create');

        $form_title = $nmtPlugin->translate("Create Opening Balance");

        $transactionType = TrxType::getGoodReceiptTrx();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $redirectUrl = null;

            $sourceWhID = (int) $this->params()->fromQuery('sourceWH');
            $movementDate = $this->params()->fromQuery('movementDate');

            $headerDTO = new TrxDTO();
            $headerDTO->movementType = TrxType::GR_FROM_OPENNING_BALANCE;
            $headerDTO->warehouse = $sourceWhID;
            $headerDTO->movementDate = $movementDate;

            $viewModel = new ViewModel(array(

                'action' => $action,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => $headerDTO,
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
        $redirectUrl = sprintf(self::ADD_ROW_URL, $dto->getId(), $dto->getToken());

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();

        $form_action = \sprintf(self::BASE_URL, 'update');
        ;
        $form_title = "Edit Good Receipt";
        $action = Constants::FORM_ACTION_EDIT;
        $viewTemplete = \sprintf(self::BASE_URL, 'crudHeader');

        $userId = $this->getUserId();
        $localCurrencyId = $this->getLocalCurrencyId();
        $transactionType = TrxType::getGoodReceiptTrx();

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
        $redirectUrl = sprintf(self::VIEW_URL, $entity_id, $entity_token);
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
        $form_action = \sprintf(self::BASE_URL, 'add-row');
        $form_title = "Add Opening Balance Row";
        $action = Constants::FORM_ACTION_ADD;
        $viewTemplete = \sprintf(self::BASE_URL, 'crudRow');
        $userId = $this->getUserId();

        $transactionType = TrxType::getGoodReceiptTrx();

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
            $this->logException($e);
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
            $viewModel->setTemplate($viewTemplete . $rootEntity->getMovementType());
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf(self::ADD_ROW_URL, $rootEntityId, $rootEntityToken);

        $this->logInfo(\sprintf("Row Trx #%s is created by %s", $rootEntityId, $userId));

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
        $form_action = \sprintf(self::BASE_URL, 'update-row');
        $form_title = "Update Good Receipt Row";
        $action = Constants::FORM_ACTION_EDIT;
        $viewTemplete = \sprintf(self::BASE_URL, 'crudRow');
        $userId = $this->getUserId();

        $transactionType = TrxType::getGoodReceiptTrx();

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
            $this->logException($e);
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

            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf(self::REVIEW_URL, $target_id, $target_token);
        return $this->redirect()->toUrl($redirectUrl);
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
        $transactionType = TrxType::getGoodReceiptTrx();

        $form_action = \sprintf(self::BASE_URL, 'saveAs');
        $form_title = "Invoice Invoice:";
        $action = null;
        $viewTemplete = \sprintf(self::BASE_URL, 'review-v1');

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->logInfo(\sprintf("Trx #%s saved as format %s by #%s", $id, $file_type, $this->getUserId()));

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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();

        $form_action = \sprintf(self::BASE_URL, 'review');
        $form_title = "Review Good Receipt";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = \sprintf(self::BASE_URL, 'review-v1');

        $transactionType = TrxType::getGoodReceiptTrx();

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

            $dto = DTOFactory::createDTOFromArray($data, new TrxDTO());

            $userId = $this->getUserId();
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
            $this->logException($e);
            $redirectUrl = sprintf(self::REVIEW_URL, $entity_id, $entity_token);
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

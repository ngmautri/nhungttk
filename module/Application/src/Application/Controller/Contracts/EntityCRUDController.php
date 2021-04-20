<?php
namespace Application\Controller\Contracts;

use Application\Notification;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Contracts\EntityCmdHandlerAbstractFactory;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Command\Options\UpdateEntityCmdOptions;
use Application\Application\Service\Contracts\EntityServiceInterface;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\FileExtension;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\Upload\UploadFactory;
use Procure\Domain\GenericDoc;
use Zend\Escaper\Escaper;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class EntityCRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $viewTemplate;

    protected $listTemplate;

    protected $ajaxLayout;

    protected $cmdHandlerFactory;

    protected $entityService;

    /**
     *
     * @return mixed
     */
    public function getViewTemplate()
    {
        return $this->viewTemplate;
    }

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

    abstract protected function setListTemplate();

    abstract protected function setViewTemplate();

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function uploadMembersAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = $this->getBaseUrl() . "/upload-rows";

        $form_title = "Upload";
        $action = FormActions::UPLOAD;
        $viewTemplete = $this->getBaseUrl() . "/review-v1";

        $request = $this->getRequest();

        if ($request->isPost()) {

            $notify = new Notification();

            try {

                $entity_id = $request->getPost('entity_id');
                $entity_token = $request->getPost('entity_token');

                /**
                 *
                 * @var GenericDoc $rootEntity ;
                 */
                $rootEntity = $this->getProcureService()->getDocHeaderByTokenId($entity_id, $entity_token);

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
                        'rootEntity' => $rootEntity,
                        'rowOutput' => $rootEntity->getRowsOutput(),
                        'headerDTO' => $rootEntity->makeDTOForGrid(),
                        'nmtPlugin' => $nmtPlugin,
                        'form_action' => $form_action,
                        'form_title' => $form_title,
                        'version' => $rootEntity->getRevisionNo(),
                        'action' => $action,
                        'localCurrencyId' => $this->getLocalCurrencyId(),
                        'defaultWarehouseId' => $this->getDefautWarehouseId(),
                        'companyVO' => $this->getCompanyVO()
                    ));
                    $viewModel->setTemplate($viewTemplete);
                    return $viewModel;
                }

                $folder = ROOT . "/temp";

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true); // important
                }

                move_uploaded_file($file_tmp, "$folder/$file_name");

                $uploader = UploadFactory::create($rootEntity->getDocType(), $this->getDoctrineEM());
                $uploader->setLogger($this->getLogger());
                $uploader->doUploading($this->getCompanyVO(), $rootEntity, "$folder/$file_name");
            } catch (\Exception $e) {
                $this->logException($e, false);
                $notify->addError($e->getMessage());

                $viewModel = new ViewModel(array(
                    'errors' => $notify->getErrors(),
                    'redirectUrl' => null,
                    'entity_id' => $entity_id,
                    'entity_token' => $entity_token,
                    'rootEntity' => $rootEntity,
                    'rowOutput' => $rootEntity->getRowsOutput(),
                    'headerDTO' => $rootEntity->makeDTOForGrid(),
                    'nmtPlugin' => $nmtPlugin,
                    'form_action' => $form_action,
                    'form_title' => $form_title,
                    'version' => $rootEntity->getRevisionNo(),
                    'action' => $action,
                    'localCurrencyId' => $this->getLocalCurrencyId(),
                    'defaultWarehouseId' => $this->getDefautWarehouseId(),
                    'companyVO' => $this->getCompanyVO()
                ));
                $viewModel->setTemplate($viewTemplete);
                return $viewModel;
            }

            $this->logInfo(\sprintf('Rows for #%s uploaded!, (%s-%s)', $rootEntity->getId(), $file_name, $file_size));
            $this->flashMessenger()->addMessage($notify->successMessage(false));
            $redirectUrl = sprintf($this->getBaseUrl() . '/review?entity_id=%s&entity_token=%s', $entity_id, $entity_token);
            \unlink("$folder/$file_name");
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO Posting
        // =========================

        $entity_id = (int) $this->params()->fromQuery('target_id');
        $entity_token = $this->params()->fromQuery('target_token');
        $rootEntity = $this->getProcureService()->getDocHeaderByTokenId($entity_id, $entity_token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $dto = $rootEntity->makeSnapshot();

        $viewModel = new ViewModel(array(
            'errors' => null,
            'redirectUrl' => null,
            'entity_id' => $entity_id,
            'entity_token' => $entity_token,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'version' => $rootEntity->getRevisionNo(),
            'action' => $action,
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO()
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/review";
        $form_title = "Edit Form";
        $action = FormActions::REVIEW;
        $viewTemplete = $this->getViewTemplate();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $this->layout("Procure/layout-fullscreen");

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

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
                'headerDTO' => $rootEntity->makeDTOForGrid(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ====================================
        try {

            $data = $prg;
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->procureService->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PostCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getPostCmdHandler();

            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();

            $msg = sprintf($cmd->getNotification()->successMessage());
            $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            $msg = $e->getMessage();
            $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        }

        if ($cmd->getNotification()->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->layout("layout/user/ajax");
        $this->flashMessenger()->addMessage($msg);
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);

        $this->logInfo($msg);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getEntityService()->getRootEntityByTokenId($id, $token, null, $this->getLocale());

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
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'entityDTO' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getCreateEntityCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'entityDTO' => $cmd->getOutput(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $entity = $cmd->getOutput();

        if ($entity != null) {
            $redirectUrl = sprintf($this->getBaseUrl() . "/add-member?target_id=%s", $entity->getId());
        } else {
            $redirectUrl = $this->getBaseUrl() . "/list";
        }

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update";
        $form_title = "Edit Form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudHeader";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $entityDTO = $this->getEntityService()->getRootEntityByTokenId($entity_id, $entity_token);

            if ($entityDTO == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'entityDTO' => $entityDTO,
                'version' => $entityDTO->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            // POSTING
            $data = $prg;

            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getEntityService()->getRootEntityByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateEntityCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateHeaderCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            // left blank
        }

        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $cmd->getOutput(),
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($cmd->getNotification()
            ->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/add-member";
        $form_title = "Add row rorm";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudMember";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->getEntityService()->getRootEntityByTokenId($target_id, $target_token);

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
                'memberDTO' => null,
                'rootDTO' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // ==============
        $notification = null;
        try {

            $data = $prg;

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $version = $data['docRevisionNo'];

            $rootEntity = $this->getEntityService()->getRootEntityByTokenId($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new CreateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $target_id, $target_token, $version, $this->getUserId(), __METHOD__);
            $options->setLocale($this->getLocale());
            $cmdHandler = $this->getCmdHandlerFactory()->getCreateMemberCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());

            $cmd->execute();
            $notification = $cmd->getNotification();
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
                'target_id' => $target_id,
                'target_token' => $target_token,
                'memberDTO' => $cmd->getOutput(),
                'DTO' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/add-member?target_id=%s&target_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("Row of %s is created by #%s", $target_id, $this->getUserId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update-row";
        $form_title = "Update row form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudRow";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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

            $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $this->getLocale());

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if ($rootDTO == null || $localDTO == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'docRevisionNo' => $rootDTO->getRevisionNo(),
                'headerDTO' => $rootDTO,
                'dto' => $localDTO, // row
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // Posting
        // =============================

        try {

            $data = $prg;

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['docRevisionNo'];

            $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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

            $options = new UpdateRowCmdOptions($this->getCompanyVO(), $rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $options->setLocale($this->getLocale());

            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
            $notification = $cmd->getNotification();
        } catch (\Exception $e) {

            $notification = new Notification();
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
                'docRevisionNo' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $cmd->getOutput(),
                'headerDTO' => $rootDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("PO Row of %s is updated by #%s", $target_id, $this->getUserId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function removeMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/remove-row";
        $form_title = "Update row form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudRow";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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
            $version = $this->params()->fromQuery('ver');

            $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $this->getLocale());

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if ($rootDTO == null || $localDTO == null) {
                $this->logInfo(\sprintf("Entity not found! %s", $entity_id));
                return $this->redirect()->toRoute('not_found');
            }

            try {
                $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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

                $options = new UpdateRowCmdOptions($this->getCompanyVO(), $rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
                $options->setLocale($this->getLocale());

                $cmdHandler = $this->getCmdHandlerFactory()->getRemoveRowCmdHandler();
                $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
                $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHanderDecorator, $this->getEventBusService());
                $cmd->setLogger($this->getLogger());
                $cmd->execute();
                $notification = $cmd->getNotification();
            } catch (\Exception $e) {
                $this->logException($e);

                $notification = new Notification();
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
                    'docRevisionNo' => $rootEntity->getRevisionNo(), // get current version.
                    'dto' => $cmd->getOutput(),
                    'headerDTO' => $rootDTO,
                    'nmtPlugin' => $nmtPlugin,
                    'form_action' => $form_action,
                    'form_title' => $form_title,
                    'action' => $action,
                    'sharedCollection' => $this->getSharedCollection(),
                    'localCurrencyId' => $this->getLocalCurrencyId(),
                    'defaultWarehouseId' => $this->getDefautWarehouseId(),
                    'companyVO' => $this->getCompanyVO()
                ));

                $viewModel->setTemplate($viewTemplete);
                return $viewModel;
            }

            $m = \sprintf("Row of %s is removed by #%s", $target_id, $this->getUserId());
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
            $this->logInfo($m);
            return $this->redirect()->toUrl($redirectUrl);
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function inlineUpdateMemberAction()
    {
        $a_json_final = array();
        $escaper = new Escaper();

        $sent_list = json_decode($_POST['sent_list'], true);
        $to_update = $sent_list['addList'];

        // $this->getLogger()->info(\serialize($to_update));
        $response = $this->getResponse();

        try {
            foreach ($to_update as $a) {

                $target_id = $a['docId'];
                $target_token = $a['docToken'];
                $entity_id = $a['id'];
                $entity_token = $a['token'];
                $version = $a['docRevisionNo'];
                // $version = $a['docVersion'];

                $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

                $rootEntity = null;
                $localEntity = null;
                $rootDTO = null;
                $localDTO = null;
                // $this->getLogger()->info(\serialize($a));

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

                $options = new UpdateRowCmdOptions($this->getCompanyVO(), $rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
                $options->setLocale($this->getLocale());

                $cmdHandler = $this->getCmdHandlerFactory()->getInlineUpdateRowCmdHandler();
                $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
                $cmd = new GenericCommand($this->getDoctrineEM(), $a, $options, $cmdHanderDecorator, $this->getEventBusService());
                $cmd->execute();

                $notification = $cmd->getNotification();
            }
        } catch (\Exception $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
            $this->getLogger()->error($e->getMessage());

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(\json_encode("[Failed] Doc not updated.Please see log!"));
            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Doc row updated!"));
        return $response;
    }

    public function cloneAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form_action = $this->getBaseUrl() . "/clone";
        $form_title = "Clone Document";
        $viewTemplete = $this->getBaseUrl() . "/crudHeader";
        $action = FormActions::EDIT;

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('entity_token');
            $dto = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $token,
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {

            // POSTING
            $data = $prg;
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateHeaderCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getCloneCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $notification = $cmd->getNotification();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $cmd->getOutput(),
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Edit Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();

        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->logInfo(\sprintf("Document #%s saved as format %s by #%s", $id, $file_type, $this->getUserId()));
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, $file_type);

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
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);

        return $viewModel;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLayout()
    {
        return $this->defaultLayout;
    }

    /**
     *
     * @return mixed
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    /**
     *
     * @return mixed
     */
    public function getAjaxLayout()
    {
        return $this->ajaxLayout;
    }

    /**
     *
     * @param EntityCmdHandlerAbstractFactory $cmdHandlerFactory
     */
    public function setCmdHandlerFactory(EntityCmdHandlerAbstractFactory $cmdHandlerFactory)
    {
        $this->cmdHandlerFactory = $cmdHandlerFactory;
    }

    /**
     *
     * @return \Application\Application\Command\Contracts\EntityCmdHandlerAbstractFactory
     */
    public function getCmdHandlerFactory()
    {
        return $this->cmdHandlerFactory;
    }

    /**
     *
     * @return \Application\Application\Service\Contracts\EntityServiceInterface
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     *
     * @param EntityServiceInterface $entityService
     */
    public function setEntityService(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }
}

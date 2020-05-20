<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\DTOFactory;
use MLA\Paginator;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\AddRowCmd;
use Procure\Application\Command\AP\AddRowCmdHandler;
use Procure\Application\Command\AP\CreateHeaderCmd;
use Procure\Application\Command\AP\CreateHeaderCmdHandler;
use Procure\Application\Command\AP\EditHeaderCmd;
use Procure\Application\Command\AP\EditHeaderCmdHandler;
use Procure\Application\Command\AP\PostCmd;
use Procure\Application\Command\AP\PostCmdHandler;
use Procure\Application\Command\AP\ReverseCmdHandler;
use Procure\Application\Command\AP\SaveCopyFromPOCmd;
use Procure\Application\Command\AP\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\AP\UpdateRowCmd;
use Procure\Application\Command\AP\UpdateRowCmdHandler;
use Procure\Application\Command\AP\Options\ApCreateOptions;
use Procure\Application\Command\AP\Options\ApPostOptions;
use Procure\Application\Command\AP\Options\ApReverseOptions;
use Procure\Application\Command\AP\Options\ApRowCreateOptions;
use Procure\Application\Command\AP\Options\ApRowUpdateOptions;
use Procure\Application\Command\AP\Options\ApUpdateOptions;
use Procure\Application\Command\AP\Options\CopyFromPOOptions;
use Procure\Application\Command\AP\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Application\Reporting\AP\ApReporter;
use Procure\Application\Service\AP\APService;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Shared\Constants;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApController extends AbstractGenericController
{

    protected $apService;

    protected $apReporter;

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function createFromPoAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/
        /**@var ApDTO $dto ;*/
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/create-from-po";
        $form_title = "Invoice from PO";
        $action = Constants::FORM_ACTION_AP_FROM_PO;
        $viewTemplete = "procure/ap/crudHeader";

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

            $errors = null;
            $dto = null;
            $version = null;

            try {
                $source_id = (int) $this->params()->fromQuery('source_id');
                $source_token = $this->params()->fromQuery('source_token');

                $options = new CopyFromPOOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);
                $rootEntity = $this->getApService()->createFromPO($source_id, $source_token, $options);

                if ($rootEntity == null) {
                    return $this->redirect()->toRoute('not_found');
                }

                $dto = $rootEntity->makeDetailsDTO();
                $version = $dto->getRevisionNo();
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            $viewModel = new ViewModel(array(
                'errors' => $errors,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'source_id' => $source_id,
                'source_token' => $source_token,
                'headerDTO' => $dto,
                'version' => $version,
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

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();
            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $version = $data['version'];

            $dto = DTOFactory::createDTOFromArray($data, new ApDTO());
            $options = new CopyFromPOOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);

            $rootEntity = $this->getApService()->createFromPO($source_id, $source_token, $options);

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
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/ap/view?entity_token=%s&entity_id=%s", $dto->getToken(), $dto->getId());
        $this->flashMessenger()->addMessage($notification->successMessage(true));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout("Procure/layout-fullscreen");
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/procure/ap/create";
        $form_title = "Create Invoice:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/ap/crudHeader";

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
                'headerDTO' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {
            $data = $prg;
            /**
             *
             * @var \Application\Entity\MlaUsers $u ;
             * @var ApDTO $dto ;
             */
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new ApDTO());
            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();

            $options = new ApCreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new CreateHeaderCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (OperationFailedException $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/ap/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addRowAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var ApRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/add-row";
        $form_title = "Add Invoice Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/ap/crudRow";

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
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($target_id, $target_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $this->getLogger()->info(\sprintf("Row AP #%s is going to be created by %s", $target_id, $u->getId()));

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'dto' => null,
                'rootDto' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $data = $prg;

            $dto = DTOFactory::createDTOFromArray($data, new ApRowDTO());

            // var_dump($dto);
            $userId = $u->getId();
            $rootEntityId = $data['target_id'];
            $rootEntityToken = $data['target_token'];
            $version = $data['version'];
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($rootEntityId, $rootEntityToken);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new ApRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHander = new AddRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHander);
            $cmd = new AddRowCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator);
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
                'rootDto' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $this->getLogger()->info(\sprintf("Row AP #%s is not created by %s. Error: %s", $rootEntityId, $u->getId(), $notification->errorMessage()));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/ap/add-row?target_id=%s&target_token=%s", $rootEntityId, $rootEntityToken);

        $this->getLogger()->info(\sprintf("Row AP #%s is created by %s", $rootEntityId, $u->getId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateRowAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var ApRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/update-row";
        $form_title = "Update Invoice Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/procure/ap/crudRow";
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
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $result = $this->getApService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;
            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }
            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }
            if (! $rootDTO instanceof ApDTO || ! $localDTO instanceof ApRowDTO) {
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
                'rootDto' => $rootDTO,
                'dto' => $localDTO, // row
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        // Posting
        // =============================
        try {
            $data = $prg;

            $dto = DTOFactory::createDTOFromArray($data, new ApRowDTO());
            $userId = $u->getId();

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $result = $this->getApService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);
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
            $options = new ApRowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new UpdateRowCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
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
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $dto,
                'rootDto' => $rootDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            $this->getLogger()->error(\sprintf("Row AP #%s is not updated by %s. Error: %s", $target_id, $u->getId(), $notification->errorMessage()));

            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/ap/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         */
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/update";
        $form_title = "Edit AP";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "procure/ap/crudHeader";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $dto = $this->getApService()->getDocHeaderByTokenId($entity_id, $entity_token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

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
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {
            // POSTING
            $data = $prg;
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new ApDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->apService->getDocHeaderByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new ApUpdateOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new EditHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new EditHeaderCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
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
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/ap/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /*
     * @return \Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/review";
        $form_title = "Review Invoice";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/ap/review";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            // echo memory_get_usage();
            // var_dump($po->makeDTOForGrid());
            // echo memory_get_usage();
            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(new ApDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
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
            $dto = DTOFactory::createDTOFromArray($data, new ApDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                $this->flashMessenger()->addMessage(\sprintf("%s-%s", $entity_id, $entity_token));
                return $this->redirect()->toRoute('not_found');
            }
            $options = new ApPostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new PostCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto->getNotification();
            $msg = sprintf("AP #%s is posted", $entity_id);
            // $redirectUrl = sprintf("/procure/ap/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $redirectUrl = "/procure/ap-report/header-status";
            http: // localhost:81/procure/ap-report/header-status
        } catch (\Exception $e) {
            $msg = sprintf("%s", $e->getMessage());
            $redirectUrl = sprintf("/procure/ap/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new ApDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        return $this->redirect()->toUrl($redirectUrl);
    }

    public function reverseAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/reverse";
        $form_title = "Reverse Invoice";
        $action = Constants::FORM_ACTION_REVERSE;
        $viewTemplete = "procure/ap/review";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            // echo memory_get_usage();
            // var_dump($po->makeDTOForGrid());
            // echo memory_get_usage();
            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(new ApDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
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

            /**
             *
             * @var ApDTO $dto ;
             */
            $dto = DTOFactory::createDTOFromArray($data, new ApDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getApService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new ApReverseOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new ReverseCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new PostCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            $notification = $dto->getNotification();
            $msg = sprintf("AP #%s is reversed", $entity_id);
            $redirectUrl = sprintf("/procure/ap/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $msg = sprintf("%s", $e->getMessage());
            $redirectUrl = sprintf("/procure/ap/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new ApDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        return $this->redirect()->toUrl($redirectUrl);

        /*
         * $this->flashMessenger()->addMessage($msg);
         * $response = $this->getResponse();
         * $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
         * $response->setContent(json_encode($redirectUrl));
         * return $response;
         */
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();
        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/create";
        $form_title = "Create Invoice:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/ap/review";

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getApService()->getDocDetailsByTokenId($id, $token);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => Constants::FORM_ACTION_SHOW,
            'form_action' => $form_action,
            'form_title' => $nmtPlugin->translate("Show Invoice"),
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(new ApDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate($viewTemplete);

        $this->getLogger()->info(\sprintf("AP #%s viewed by #%s", $id, $u->getId()));
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/view";
        $form_title = "Create Invoice:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/ap/review";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->getLogger()->info(\sprintf("AP #%s saved as format %s by #%s", $id, $file_type, $u->getId()));

        $rootEntity = $this->getApService()->getDocDetailsByTokenId($id, $token, $file_type);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => Constants::FORM_ACTION_SHOW,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate($viewTemplete);

        return $viewModel;
    }

    public function printAction()
    {
        $this->layout("Procure/layout-fullscreen");
        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');
        $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($id, $token, $file_type);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => \Procure\Domain\Shared\Constants::FORM_ACTION_SHOW,
            'form_action' => "/procure/po/view",
            'form_title' => $nmtPlugin->translate("Show PO"),
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate("procure/po/review-v1");
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
        $total_records = $this->getApReporter()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getApReporter()->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset, $file_type);

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

        $viewModel->setTemplate("procure/ap/dto_list");
        return $viewModel;
    }

    // =========================================================

    /**
     *
     * @return \Procure\Application\Service\AP\APService
     */
    public function getApService()
    {
        return $this->apService;
    }

    /**
     *
     * @param APService $apService
     */
    public function setApService(APService $apService)
    {
        $this->apService = $apService;
    }

    /**
     *
     * @return \Procure\Application\Reporting\AP\ApReporter
     */
    public function getApReporter()
    {
        return $this->apReporter;
    }

    /**
     *
     * @param ApReporter $apReporter
     */
    public function setApReporter(ApReporter $apReporter)
    {
        $this->apReporter = $apReporter;
    }
}

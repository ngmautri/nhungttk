<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Domain\Shared\DTOFactory;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Monolog\Logger;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\QR\AddRowCmdHandler;
use Procure\Application\Command\QR\CreateHeaderCmdHandler;
use Procure\Application\Command\QR\EditHeaderCmdHandler;
use Procure\Application\Command\QR\PostCmdHandler;
use Procure\Application\Command\QR\ReverseCmdHandler;
use Procure\Application\Command\QR\UpdateRowCmdHandler;
use Procure\Application\Command\QR\Options\CreateOptions;
use Procure\Application\Command\QR\Options\PostOptions;
use Procure\Application\Command\QR\Options\ReverseOptions;
use Procure\Application\Command\QR\Options\RowCreateOptions;
use Procure\Application\Command\QR\Options\RowUpdateOptions;
use Procure\Application\Command\QR\Options\UpdateOptions;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Application\DTO\Qr\QrRowDTO;
use Procure\Application\Reporting\QR\QrReporter;
use Procure\Application\Service\QR\QRService;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Shared\Constants;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrController extends AbstractActionController
{

    protected $doctrineEM;

    protected $qrService;

    protected $eventBusService;

    protected $logger;

    protected $qrReporter;

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var QrDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));
        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/procure/qr/create";
        $form_title = "Create Quotation:";
        $action = Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/qr/crudHeader";

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

            $dto = DTOFactory::createDTOFromArray($data, new QrDTO());
            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();

            $options = new CreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
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
            $this->getLogger()->info(\sprintf("Quotation not created. Error:%s", $notification->errorMessage()));
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/qr/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());
        $this->getLogger()->info(\sprintf("Quotation #%s created by %s", $dto->getId(), $u->getId()));
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
        $form_action = "/procure/qr/update";
        $form_title = "Edit Quotationi";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "procure/qr/crudHeader";

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
            $dto = $this->getQrService()->getDocHeaderByTokenId($entity_id, $entity_token);

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
            $dto = DTOFactory::createDTOFromArray($data, new QrDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getQrService()->getDocHeaderByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new UpdateOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new EditHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
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
            $this->getLogger()->error(\sprintf("Quotation not updated. Error: %s", $notification->errorMessage()));
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/qr/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        $this->getLogger()->info(\sprintf("Quotation #%s updated by %s", $dto->getId(), $u->getId()));

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
         * @var QrRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/add-row";
        $form_title = "Add quotation row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/qr/crudRow";

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
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($target_id, $target_token);
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

            $dto = DTOFactory::createDTOFromArray($data, new QrRowDTO());

            // var_dump($dto);
            $userId = $u->getId();
            $rootEntityId = $data['target_id'];
            $rootEntityToken = $data['target_token'];
            $version = $data['version'];
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($rootEntityId, $rootEntityToken);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new RowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHander = new AddRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHander);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
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
            $this->getLogger()->info(\sprintf("Row of Quotation #%s is not created. Error: %s", $rootEntityId, $notification->errorMessage()));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/qr/add-row?target_id=%s&target_token=%s", $rootEntityId, $rootEntityToken);

        $this->getLogger()->info(\sprintf("Row #%s is created by %s", $rootEntityId, $u->getId()));

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
         * @var QrRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/update-row";
        $form_title = "Update Quoation Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/procure/qr/crudRow";
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
            $result = $this->getQrService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;
            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }
            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }
            if (! $rootDTO instanceof QrDTO || ! $localDTO instanceof QrRowDTO) {
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

            $dto = DTOFactory::createDTOFromArray($data, new QrRowDTO());
            $userId = $u->getId();

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $result = $this->getQrService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);
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
            $options = new RowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
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
            $this->getLogger()->error(\sprintf("Row #%s is not updated by %s. Error: %s", $target_id, $u->getId(), $notification->errorMessage()));

            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/qr/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        $this->getLogger()->error(\sprintf("Row #%s is updated by %s.", $target_id, $u->getId()));
        return $this->redirect()->toUrl($redirectUrl);
    }

    /*
     * @return \Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         */
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/review";
        $form_title = "Review Quotation";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/qr/review";

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
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new QrDTO()),
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

            $dto = DTOFactory::createDTOFromArray($data, new QrDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                $this->flashMessenger()->addMessage(\sprintf("%s-%s", $entity_id, $entity_token));
                return $this->redirect()->toRoute('not_found');
            }
            $options = new PostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto->getNotification();
            $msg = sprintf("quoation #%s is posted", $entity_id);
            $redirectUrl = sprintf("/procure/qr/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $msg = sprintf("%s", $e->getMessage());
            $redirectUrl = sprintf("/procure/qr/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new QrDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            $this->getLogger()->error(\sprintf("#%s is not posted by %s. Error: %s", $entity_id, $u->getId(), $notification->errorMessage()));

            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        return $this->redirect()->toUrl($redirectUrl);
    }

    public function reverseAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var QrDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/reverse";
        $form_title = "Reverse Quoation";
        $action = Constants::FORM_ACTION_REVERSE;
        $viewTemplete = "procure/qr/review";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new QrDTO()),
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

            $dto = DTOFactory::createDTOFromArray($data, new QrDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getQrService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new ReverseOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new ReverseCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto->getNotification();
            $msg = sprintf("AP #%s is reversed", $entity_id);
            $redirectUrl = sprintf("/procure/qr/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $msg = sprintf("%s", $e->getMessage());
            $redirectUrl = sprintf("/procure/qr/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
                'headerDTO' => $rootEntity->makeDTOForGrid(new QrDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            $this->getLogger()->error(\sprintf("#%s is not reversed by %s. Error: %s", $entity_id, $u->getId(), $notification->errorMessage()));

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
        /**@var \Application\Entity\MlaUsers $u ;*/
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();
        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/create";
        $form_title = "Show Quotion:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/qr/review";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getQrService()->getDocDetailsByTokenId($id, $token);
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
            'headerDTO' => $rootEntity->makeDTOForGrid(new QrDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate($viewTemplete);

        $this->getLogger()->info(\sprintf("Quotation #%s viewed by #%s", $id, $u->getId()));
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {

        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         */
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/qr/view";
        $form_title = "Create Quoation:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/qr/review";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->getLogger()->info(\sprintf("Quotation #%s saved as format %s by #%s", $id, $file_type, $u->getId()));

        $rootEntity = $this->getQrService()->getDocDetailsByTokenId($id, $token, $file_type);
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

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
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


        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $current_state = null;
        $filter_by = null;
        $paginator = null;
        $limit = null;
        $offset = null;

        /**
         *
         * @todo: CACHE
         */
        $total_records = $this->getQrReporter()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->qrReporter->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

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

        $viewModel->setTemplate("procure/qr/dto_list");
        return $viewModel;
    }

    // ==================================================

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
    }

    /**
     *
     * @return \Procure\Application\Eventbus\EventBusService
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }

    /**
     *
     * @param \Procure\Application\Eventbus\EventBusService $eventBusService
     */
    public function setEventBusService(\Procure\Application\Eventbus\EventBusService $eventBusService)
    {
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return \Procure\Application\Service\QR\QRService
     */
    public function getQrService()
    {
        return $this->qrService;
    }

    /**
     *
     * @param QRService $qrService
     */
    public function setQrService(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     *
     * @return \Procure\Application\Reporting\QR\QrReporter
     */
    public function getQrReporter()
    {
        return $this->qrReporter;
    }

    /**
     *
     * @param QrReporter $qrReporter
     */
    public function setQrReporter(QrReporter $qrReporter)
    {
        $this->qrReporter = $qrReporter;
    }
}

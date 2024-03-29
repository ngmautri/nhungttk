<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\DTOFactory;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Endroid\QrCode\QrCode;
use Application\Domain\Util\Pagination\Paginator;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\PR\AddRowCmdHandler;
use Procure\Application\Command\PR\CreateHeaderCmdHandler;
use Procure\Application\Command\PR\EditHeaderCmdHandler;
use Procure\Application\Command\PR\PostCmdHandler;
use Procure\Application\Command\PR\UpdateRowCmdHandler;
use Procure\Application\Command\PR\Options\CreateOptions;
use Procure\Application\Command\PR\Options\PostOptions;
use Procure\Application\Command\PR\Options\RowCreateOptions;
use Procure\Application\Command\PR\Options\RowUpdateOptions;
use Procure\Application\Command\PR\Options\UpdateOptions;
use Procure\Application\DTO\Pr\PrDTO;
use Procure\Application\DTO\Pr\PrRowDTO;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\PR\PRService;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Symfony\Component\Workflow\Exception\LogicException;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends AbstractGenericController
{

    const QR_CODE_PATH = "/data/procure/qr_code/pr/";

    protected $prService;

    protected $prReporter;

    protected $attachmentService;

    protected $purchaseRequestService;

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
         * @var PrDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/procure/pr/create";
        $form_title = "Create Purchase Request:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/crudHeader";

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

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new PrDTO());
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
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/pr/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());
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
         * @var \Application\Entity\MlaUsers $u ;
         */
        $this->layout("Procure/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/update";
        $form_title = "Edit PR";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "procure/pr/crudHeader";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $dto = $this->getPurchaseRequestService()->getDocHeaderByTokenId($entity_id, $entity_token);

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
            $dto = DTOFactory::createDTOFromArray($data, new PrDTO());
            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $rootEntity = $this->getPurchaseRequestService()->getDocHeaderByTokenId($entity_id, $entity_token);

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
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/pr/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
         * @var \Application\Entity\MlaUsers $u ;
         * @var PrRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/add-row";
        $form_title = "Add PR Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/crudRow";

        $prg = $this->prg($form_action, true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenIdFromDB($target_id, $target_token);
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
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $data = $prg;
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $dto = DTOFactory::createDTOFromArray($data, new PrRowDTO());

            // var_dump($dto);
            $userId = $u->getId();
            $rootEntityId = $data['target_id'];
            $rootEntityToken = $data['target_token'];
            $version = $data['version'];
            $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenIdFromDB($rootEntityId, $rootEntityToken);

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
                'headerDTO' => $rootEntity->makeHeaderDTO(),
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
        $redirectUrl = sprintf("/procure/pr/add-row?target_id=%s&target_token=%s", $rootEntityId, $rootEntityToken);
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
         * @var \Application\Entity\MlaUsers $u ;
         * @var PrRowDTO $dto ;
         */
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/update-row";
        $form_title = "Update PR Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/procure/pr/crudRow";

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
            $result = $this->getPurchaseRequestService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

            $headerDTO = null;
            $localDTO = null;
            if (isset($result["rootDTO"])) {
                $headerDTO = $result["rootDTO"];
            }
            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }
            if (! $headerDTO instanceof PrDTO || ! $localDTO instanceof PrRowDTO) {
                return $this->redirect()->toRoute('not_found');
            }
            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $headerDTO->getRevisionNo(),
                'headerDTO' => $headerDTO,
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

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new PrRowDTO());
            $userId = $u->getId();

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $result = $this->getPurchaseRequestService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);
            $rootEntity = null;
            $localEntity = null;
            $headerDTO = null;
            $localDTO = null;

            if (isset($result["rootEntity"])) {
                $rootEntity = $result["rootEntity"];
            }
            if (isset($result["localEntity"])) {
                $localEntity = $result["localEntity"];
            }
            if (isset($result["rootDTO"])) {
                $headerDTO = $result["rootDTO"];
            }
            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }
            if ($rootEntity == null || $localEntity == null || $headerDTO == null || $localDTO == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new RowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setCache($this->getCache());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            // echo $e->getTraceAsString();
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
                'headerDTO' => $headerDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/pr/view?entity_id=%s&entity_token=%s", $target_id, $target_token);
        return $this->redirect()->toUrl($redirectUrl);
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
        $form_action = "/procure/pr/view";
        $form_title = "Show PR:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/review-v1";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');
        $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenId($id, $token, $file_type);
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
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/view";
        $form_title = "Show PR:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/review-v1";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');
        $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenId($id, $token, $file_type);
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
            'headerDTO' => $rootEntity->makeDTOForGrid(new PrDTO()),
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
    public function reviewAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/review";
        $form_title = "Review PR";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/pr/review-v1";

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

            $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenIdFromDB($entity_id, $entity_token);

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
                'headerDTO' => $rootEntity->makeDTOForGrid(new PrDTO()),
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

        $notification = new Notification();
        try {

            $msg = null;
            $data = $prg;

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $userId = $u->getId();

            $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenIdFromDB($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $dto = new PrDTO();
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);

            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setCache($this->getCache());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
            $this->getLogger()->info(\sprintf("PR #%s posted by #%s", $entity_id, $u->getId()));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $notification->addError($msg);
        }

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(new PrDTO()),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/pr/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        $this->flashMessenger()->addMessage($notification->successMessage(true));
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

        $method = $this->params()->fromQuery('method', 'get');

        switch ($method) {
            case 'get':

                $response = $this->getResponse();
                $client->setUri('http://localhost:8983/solr/inventory_item/select?rows=1000');
                $client->setMethod('GET');
                $client->setParameterGET(array(
                    'q' => "name:kim"
                ));
                break;

            case 'get-list':
                $client->setMethod('GET');
                $client->setUri('http://localhost:8983/solr/inventory_item/select');
                break;
            case 'create':

                $data = array(
                    "name" => "Laos Finance manager",
                    "name" => "kim"
                );

                $request = new Request();
                $request->setUri('http://localhost:8983/solr/inventory_item/update/json/docs?commit=true');
                $request->setMethod('POST');
                $request->setContent(json_encode($data));

                $request->getHeaders()->addHeaders(array(
                    'Content-Type' => 'application/json'
                ));

                // $client->setHeaders('Content-type','application/json');
                $client->setEncType(HttpClient::ENC_FORMDATA);

                // if get/get-list/create
                $response = $client->send($request);

                if (! $response->isSuccess()) {
                    // report failure
                    $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
                    $message .= $response->getContent();
                    $message = $message . $client->getMethod();
                    $message = $message . "...........NO unknown....";

                    $response = $this->getResponse();
                    $response->setContent($message);
                    return $response;
                }

                $body = $response->getBody();

                $response = $this->getResponse();
                $response->setContent($body);

                return $response;

            case 'update':
                $data = array(
                    'name' => 'ikhsan'
                );
                $adapter = $client->getAdapter();

                $adapter->connect('localhost', 80);
                $uri = $client->getUri() . '?id=1';
                // send with PUT Method, with $data parameter
                $adapter->write('PUT', new \Zend\Uri\Uri($uri), 1.1, array(), http_build_query($data));

                $responsecurl = $adapter->read();
                list ($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();

                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);

                return $response;
            case 'delete':
                $adapter = $client->getAdapter();

                $adapter->connect('localhost', 80);
                $uri = $client->getUri() . '?id=1'; // send parameter id = 1
                                                    // send with DELETE Method
                $adapter->write('DELETE', new \Zend\Uri\Uri($uri), 1.1, array());

                $responsecurl = $adapter->read();
                list ($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();

                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);

                return $response;
        }

        $response = $client->send();

        if (! $response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
            $message = $message . $client->getMethod();
            $message = $message . "...........NO unknown....";

            $response = $this->getResponse();
            $response->setContent($message);
            return $response;
        }

        $body = $response->getBody();

        $response = $this->getResponse();
        $response->setContent($body);

        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');
        $prYear = $this->params()->fromQuery('yy');
        $balance = $this->params()->fromQuery('balance');

        if ($prYear == null) {
            $prYear = date("Y");
        }

        if ($balance == null) {
            $balance = 1;
        }

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

        $isActive = (int) $this->params()->fromQuery('is_active');

        if ($isActive == null) {
            $isActive = 1;
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($prYear);
        $filter->setBalance($balance);

        $total_records = $this->getPrReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
            $offset = $paginator->getMinInPage() - 1;
        }

        $list = $this->getPrReporter()->getListWithCustomDTO($filter, $sort_by, $sort, $limit, $offset, $file_type);

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $isActive,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus,
            'yy' => $prYear,
            'balance' => $balance
        ));

        $viewModel->setTemplate("procure/pr/dto_list");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/view";
        $form_title = "View Purchase Request:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/review-v1";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByTokenId($id, $token);
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
            'headerDTO' => $rootEntity->makeDTOForGrid(new PrDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'entity_id' => null,
            'entity_token' => null
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function view1Action()
    {
        $this->layout("layout/user/ajax");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/view1";
        $form_title = "View Purchase Request:";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/pr/view-v1";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        $id = (int) $this->params()->fromQuery('entity_id');
        $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByIdFromDB($id, SaveAsSupportedType::OUTPUT_IN_ARRAY);
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
            'headerDTO' => $rootEntity->makeDTOForGrid(new PrDTO()),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'entity_id' => null,
            'entity_token' => null
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    // =================================
    // @deprecated
    // =================================

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();

        // var_dump($criteria);

        $sort_criteria = array();

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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("project_" . $entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        /**
         *
         * @todo : update index
         */
        // $this->employeeSearchService->createEmployeeIndex();

        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function priceMatchingAction()
    {
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // Is Posting .................
        // ============================

        if ($request->isPost()) {
            $errors = array();
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new NmtProcurePr();
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->prService->saveHeader($entity, $data, $u, TRUE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/pr/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] PR #%s - %s created', $entity->getId(), $entity->getPrAutoNumber());

            // create QR_CODE
            $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();

            // $name_part1 = Rand::getString ( 6, \Application\Model\Constants::CHAR_LIST, true ) . "_" . Rand::getString ( 10, \Application\Model\Constants::CHAR_LIST, true );
            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];

            $folder = ROOT . self::QR_CODE_PATH . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR;

            if (! is_dir($folder)) {
                mkdir($folder, 0777, true); // important
            }

            $qrCode = new QrCode($redirectUrl);
            $qrCode->setSize(100);
            $qrCode->writeFile($folder . $qr_code_name);

            $redirectUrl = "/procure/pr-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ++++++++++++++++++++++++++

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $entity = new NmtProcurePr();
        $entity->setIsActive(1);
        $entity->setIsDraft(1);
        $entity->setWarehouse($u->getCompany()
            ->getDefaultWarehouse());
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate("procure/pr/crud");
        return $viewModel;
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function allAction()
    {
        // $this->layout ( "layout/fluid" );
        // $plugin = $this->ProcureWfPlugin();
        // echo($plugin->getWF());

        // $this->layout("Procure/layout-fluid-1");

        // echo \Application\Model\Constants::v4();
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $pr_year = $this->params()->fromQuery('pr_year');

        $is_active = (int) $this->params()->fromQuery('is_active');

        $status = $this->getEvent()
            ->getRouteMatch()
            ->getParam("status");
        $row_number = (int) $this->getEvent()
            ->getRouteMatch()
            ->getParam("row_number");

        if ($is_active == null) :
            $is_active = 1;
		endif;

        if ($balance == null) :
            $balance = 1;
		endif;

        if ($status == "pending") {
            $balance = 1;
        } elseif ($status == "completed") {
            $balance = 0;
        } elseif ($status == "all") {
            $balance = 2;
        }

        // echo $balance;

        if ($row_number == 0) {
            if ($sort_by == null) :
                $sort_by = "submittedOn"; endif;

            if ($sort == null) :
                $sort = "DESC";endif;

        } else {
            if ($sort_by == null) :
                $sort_by = "submittedOn";

                if ($sort == null) :
                    $sort = "DESC";
            endif;
            endif;


        }

        if ($pr_year == null) :
            // $pr_year = date('Y');
            $pr_year = 0;
        endif;

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

        /** @var \Doctrine\ORM\EntityManager $doctrineEM ;*/
        $doctrineEM = $this->NmtPlugin()->doctrineEM();

        /** @var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');

        $list = $res->getPrList($row_number, $pr_year, $is_active, $balance, $sort_by, $sort, 0, 0);

        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $res->getPrList($row_number, $pr_year, $is_active, $balance, $sort_by, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'balance' => $balance,
            'is_active' => $is_active,
            'status' => $status,
            'pr_year' => $pr_year,
            'row_number' => $row_number
            // 'uid'=>\Application\Model\Constants::v4(),
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        \trigger_error(\sprintf("@deprecated"));

        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('entity_id');
        // $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $pr = $res->getPrNew($id, $token);

        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }

        if ($entity instanceof \Application\Entity\NmtProcurePr) {

            try {
                /** @var \Symfony\Component\Workflow\Workflow $wf */
                // $wf = $this->ProcureWfPlugin()->createWorkflow($entity);

                // var_dump($wf->getEnabledTransitions($entity));

                // $wf->apply($entity, "recall");
                // $dumper = new GraphvizDumper();
                // echo $dumper->dump($wf->getDefinition());

                /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                $wf_plugin = $this->WfPlugin();

                /** @var \Workflow\Service\WorkflowService $wfService */
                $wfService = $wf_plugin->getWorkflowSerive();

                /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $wf_factory */
                $wf_factory = $wfService->getWorkFlowFactory($entity);

            /** @var \Symfony\Component\Workflow\Workflow  $wf */
                // $wf = $wf_factory->makePrSendingWorkflow()->createWorkflow();
                // $wf->apply($entity,"send");
            } catch (LogicException $e) {
                // echo $e->getMessage();
            }
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'total_row' => $pr['total_row'],
                'max_row_number' => $pr['max_row_number'],
                'active_row' => $pr['active_row'],
                'total_attachment' => $pr['total_attachment'],
                'total_picture' => $pr['total_picture'],
                'nmtPlugin' => $nmtPlugin
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
    public function submitAction()
    {
        \trigger_error(\sprintf("@deprecated"));
        /*
         * $request = $this->getRequest();
         *
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         */
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->downloadPrRows($id, $token);

        if ($rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
        $wf_plugin = $this->WfPlugin();

        /** @var \Workflow\Service\WorkflowService $wfService */
        $wfService = $wf_plugin->getWorkflowSerive();

        /** @var \Application\Entity\NmtProcurePr $pr ; */
        $pr = null;
        if ($rows[0][0] instanceof NmtProcurePrRow) {

            $pr = $rows[0][0]->getPr();
        }

        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            try {

                /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $pr_wf_factory */
                $pr_wf_factory = $wfService->getWorkFlowFactory($pr);

                /** @var \Symfony\Component\Workflow\Workflow  $wf */
                $wf = $pr_wf_factory->makePrSendingWorkflow()->createWorkflow();
                $wf->apply($pr, "submit");
            } catch (LogicException $e) {
                $this->flashMessenger()->addMessage($e->getMessage());
                $url = "/procure/pr/show?token=" . $token . "&entity_id=" . $id;
                // return $this->redirect()->toUrl($url);
            }
        }

        foreach ($rows as $r) {

            $entity = $r[0];
            $errors = array();

            try {

                /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                $wf_plugin = $this->WfPlugin();

                /** @var \Workflow\Service\WorkflowService $wfService */
                $wfService = $wf_plugin->getWorkflowSerive();

                /** @var \Workflow\Workflow\Procure\Factory\PrRowWorkflowFactoryAbstract $wf_factory */
                $wf_factory = $wfService->getWorkFlowFactory($entity);

                /** @var \Symfony\Component\Workflow\Workflow  $wf */
                $wf = $wf_factory->makePrRowWorkFlow()->createWorkflow();
                $wf->apply($entity, "submit");
            } catch (LogicException $e) {
                // echo $e->getMessage();
                $errors[] = $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            $m = "<ul>";
            foreach ($errors as $error) {
                $m = $m . "<li>" . $error . "</li>";
            }
            $m = $m . "</ul>";

            $this->flashMessenger()->addMessage($m);
        } else {
            $this->flashMessenger()->addMessage("PR is submited!");
        }

        $url = "/procure/pr/show?token=" . $token . "&entity_id=" . $id;
        // return $this->redirect()->toUrl($url);
    }

    /**
     * * @deprecated
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function grAction()
    {
        // $plugin = $this->ProcureWfPlugin();
        // $wf = $plugin->getWF();
        $request = $this->getRequest();
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        if ($entity !== null) {

            try {
            /** @var \Symfony\Component\Workflow\Workflow $wf */
                // $wf = $this->ProcureWfPlugin()->createWorkflow($entity);

                // var_dump($wf->getEnabledTransitions($entity));

                // $wf->apply($entity, "recall");
                // $dumper = new GraphvizDumper();
                // echo $dumper->dump($wf->getDefinition());

            /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                // $wf_plugin = $this->WfPlugin();

            /** @var \Workflow\Service\WorkflowService $wfService */
                // $wfService = $wf_plugin->getWorkflowSerive();

            /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $wf_factory */
                // $wf_factory = $wfService->getPrWorkFlowFactory($entity);

            /** @var \Symfony\Component\Workflow\Workflow  $wf */
                // $wf = $wf_factory->makePrSendingWorkflow();
                // $wf->apply($entity,"get");
            } catch (LogicException $e) {
                // echo $e->getMessage();
            }
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
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

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $token = $data['entity_token'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcurePr $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity not found';
                $this->flashMessenger()->addMessage('Something went wrong!');

                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $this->prService->saveHeader($entity, $data, $u, false, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/pr/crud");
                return $viewModel;
            }

            $m = sprintf('"PR #%s - %s" updated', $entity->getId(), $entity->getPrAutoNumber());

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ========================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        if (! $entity instanceof \Application\Entity\NmtProcurePr) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'n' => 0,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/pr/crud");
        return $viewModel;
    }

    /**
     * * @deprecated
     *
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    public function getPqCodePngAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtProcurePr $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);

        if ($entity !== null) {

            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR;
            $folder_relative = $folder_relative . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];

            $folder = ROOT . self::QR_CODE_PATH . $folder_relative . DIRECTORY_SEPARATOR . $qr_code_name;
            if (! file_exists($folder)) {
                return;
            }
            // echo $folder;
            
            /**
             * Important! for UBUNTU
             */
            $folder = str_replace('\\', '/', $folder);
            

            $imageContent = file_get_contents($folder);
            $response = $this->getResponse();
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', 'image/png')
                ->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        } else {
            return;
        }
    }

    /**
     *
     * @deprecated
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function printPdfAction()
    {
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtProcurePr $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);

        if ($entity !== null) {

            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR;
            $folder_relative = $folder_relative . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];

            /*
             * $folder = ROOT . self::QR_CODE_PATH . $folder_relative . DIRECTORY_SEPARATOR . $qr_code_name;
             * if (! file_exists($folder)) {
             * return;
             * }
             */

            // echo $folder;

            $details = 'If you can see this PDF file, the PDF service has been configurated successfully! :-)';
            $image_file = '';
            // $image_file = $folder;
            $content = $this->pdfService->printPrPdf($details, $image_file);

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/x-pdf');
            $response->setContent($content);
            return $response;
        } else {
            return;
        }
    }

    // =================================
    // Setter and getter
    // =================================

    /**
     *
     * @return \Procure\Service\PrService
     */
    public function getPrService()
    {
        return $this->prService;
    }

    /**
     *
     * @param \Procure\Service\PrService $prService
     */
    public function setPrService(\Procure\Service\PrService $prService)
    {
        $this->prService = $prService;
    }

    /**
     *
     * @return \Procure\Application\Service\PR\PRService
     */
    public function getPurchaseRequestService()
    {
        return $this->purchaseRequestService;
    }

    /**
     *
     * @param PRService $purchaseRequestService
     */
    public function setPurchaseRequestService(PRService $purchaseRequestService)
    {
        $this->purchaseRequestService = $purchaseRequestService;
    }

    /**
     *
     * @return \Procure\Application\Reporting\PR\PrReporter
     */
    public function getPrReporter()
    {
        return $this->prReporter;
    }

    /**
     *
     * @param PrReporter $prReporter
     */
    public function setPrReporter(PrReporter $prReporter)
    {
        $this->prReporter = $prReporter;
    }
}

<?php
namespace Procure\Controller\Contracts;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\PO\Options\PoCreateOptions;
use Procure\Application\Service\Contracts\ProcureServiceInterface;
use Zend\View\Model\ViewModel;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class ProcureCRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $listTemplate;

    protected $ajaxLayout;

    protected $cmdHandlerFactory;

    protected $procureService;

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

    abstract protected function setListTemplate();

    public function createAction()
    {
        $this->layout("Procure/layout-fullscreen");

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
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
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CreateHeaderCmdOptions($this->getCompanyId(), $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getCreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
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
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => $cmd->getOutput(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());
        $this->getLogger()->info(\sprintf("PO #%s is created by #%s", $dto->getId(), $this->getId()));

        return $this->redirect()->toUrl($redirectUrl);
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
     * @return \Procure\Application\Service\Contracts\ProcureServiceInterface
     */
    public function getProcureService()
    {
        return $this->procureService;
    }

    /**
     *
     * @param ProcureServiceInterface $procureService
     */
    public function setProcureService(ProcureServiceInterface $procureService)
    {
        $this->procureService = $procureService;
    }

    /**
     *
     * @param CmdHandlerAbstractFactory $cmdHandlerFactory
     */
    public function setCmdHandlerFactory(CmdHandlerAbstractFactory $cmdHandlerFactory)
    {
        $this->cmdHandlerFactory = $cmdHandlerFactory;
    }

    /**
     *
     * @return \Procure\Application\Command\Contracts\CmdHandlerAbstractFactory
     */
    public function getCmdHandlerFactory()
    {
        return $this->cmdHandlerFactory;
    }
}

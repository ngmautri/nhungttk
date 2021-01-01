<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Domain\Contracts\FormActions;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\AP\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\Contracts\ApServiceInterface;
use Procure\Controller\Contracts\ProcureCRUDController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApController extends ProcureCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
        $this->setViewTemplate();
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/procure/ap';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/ap/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/ap/dto_list';
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function createFromPoAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var ApDTO $dto ;*/
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/ap/create-from-po";
        $form_title = "Invoice from PO";
        $action = FormActions::AP_FROM_PO;
        $viewTemplete = "procure/ap/crudHeader";

        /**
         *
         * @var ApServiceInterface $apService ;
         */
        $apService = $this->getProcureService();

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

                $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
                $rootEntity = $apService->createFromPO($source_id, $source_token, $options);

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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ===============================

        try {
            $data = $prg;
            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $version = $data['version'];

            $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);

            $rootEntity = $apService->createFromPO($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromPOCmdHandler();
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
                'source_id' => $source_id,
                'source_token' => $source_token,
                'headerDTO' => $cmd->getOutput(),
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/ap/view?entity_token=%s&entity_id=%s", $cmd->getOutput()->getToken(), $cmd->getOutput()->getId());
        $this->flashMessenger()->addMessage($notification->successMessage());

        return $this->redirect()->toUrl($redirectUrl);
    }
}

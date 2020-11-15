<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\PO\SaveCopyFromQuoteCmdHandler;
use Procure\Application\Command\PO\Options\CopyFromQuoteOptions;
use Procure\Application\Command\PO\Options\SaveCopyFromQuoteOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Controller\Contracts\ProcureCRUDController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoController extends ProcureCRUDController
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
        $this->baseUrl = '/procure/po';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/po/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/po/dto_list';
    }

    public function createFromQrAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/
        /**@var PoDTO $dto ;*/
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create-from-qr";
        $form_title = "PO from Quote";
        $action = Constants::FORM_ACTION_PO_FROM_QO;
        $viewTemplete = "procure/po/crudHeader";

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

            $options = new CopyFromQuoteOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);
            $rootEntity = $this->getPurchaseOrderService()->createFromQuotation($source_id, $source_token, $options);

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

            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $options = new CopyFromQuoteOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);

            $rootEntity = $this->getPurchaseOrderService()->createFromQuotation($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromQuoteOptions($companyId, $userId, __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromQuoteCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
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

        $redirectUrl = sprintf("/procure/po/view?entity_token=%s&entity_id=%s", $dto->getToken(), $dto->getId());
        $this->flashMessenger()->addMessage($notification->successMessage(true));

        return $this->redirect()->toUrl($redirectUrl);
    }
}

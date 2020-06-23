<?php
namespace Inventory\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Association\CreateCmdHandler;
use Inventory\Application\Command\Association\UpdateCmdHandler;
use Inventory\Application\Command\Association\Options\CreateOptions;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Association\AssociationDTO;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\Service\Association\AssociationService;
use MLA\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationItemController extends AbstractGenericController
{

    protected $associationService;

    /**
     *
     * @return \Inventory\Application\Service\Association\AssociationService
     */
    public function getAssociationService()
    {
        return $this->associationService;
    }

    /**
     *
     * @param AssociationService $associationService
     */
    public function setAssociationService(AssociationService $associationService)
    {
        $this->associationService = $associationService;
    }

    public function viewAction()
    {
        $u = $this->getUser();
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        // $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "";
        $form_title = "Show item:";
        $action = Constants::FORM_ACTION_SHOW;
        $viewTemplete = "inventory/item/crud-v1";

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getItemService()->getDocDetailsByTokenId($id, $token);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => Constants::FORM_ACTION_SHOW,
            'form_action' => $action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'dto' => $rootEntity->makeSnapshot(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'tab_id' => __FUNCTION__
        ));
        $viewModel->setTemplate($viewTemplete);

        $this->getLogger()->info(\sprintf("AP #%s viewed by #%s", $id, $u->getId()));
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        // $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form_action = '/inventory/association-item/create';
        $form_title = 'Create Association Item';
        $action = \Application\Model\Constants::FORM_ACTION_ADD;
        $viewTemplete = "inventory/association-item/crud";

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
                'dto' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'version' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = new Notification();
        try {
            $data = $prg;
            $dto = SnapshotAssembler::createSnapShotFromArray($data, new AssociationDTO());
            $options = new CreateOptions($this->getCompanyId(), $this->getUserId(), __METHOD__);

            $cmdHandler = new CreateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'dto' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'version' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/inventory/item/list2";

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $u = $this->getUser();

        $nmtPlugin = $this->Nmtplugin();
        $prg = $this->prg('/inventory/item/update', true);
        $form_action = "/inventory/item/update";
        $form_title = "Edit Item";

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {

            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $rootEntity = $this->getItemService()->getDocDetailsByTokenId($entity_id, $entity_token);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto' => $rootEntity->makeSnapshot(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'version' => $rootEntity->getRevisionNo(),
                'tab_id' => __FUNCTION__
            ));

            $viewModel->setTemplate("inventory/item/crud-v1");
            return $viewModel;
        }

        // ==========================

        $notification = new Notification();
        try {

            $data = $prg;
            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $dto = DTOFactory::createDTOFromArray($data, new ItemDTO());
            $rootEntity = $this->getItemService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new UpdateItemOptions($rootEntity, $entity_id, $entity_token, $version, $u->getId(), __METHOD__);

            $cmdHandler = new UpdateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => $redirectUrl,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'tab_id' => __FUNCTION__
            ));

            $viewModel->setTemplate("inventory/item/crud-v1");
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/inventory/item/list2";
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $this->layout("layout/user/ajax");
        }

        $entity_token = $this->params()->fromQuery('entity_token');
        $entity_id = $this->params()->fromQuery('entity_id');

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
        $limit = null;
        $offset = null;

        $result = $this->getAssociationService()->getAssociationOf($entity_id, $limit, $offset);

        $total_records = count($result);

        $paginator = null;
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $result = $this->getAssociationService()->getAssociationOf($entity_id, $limit, $offset);

        return new ViewModel(array(
            'list' => $result,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'entity_token' => $entity_token,
            'entity_id' => $entity_id
        ));
    }

    public function list1Action()
    {
        echo "hello";
    }
}

<?php
namespace Inventory\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Item\UpdateCmdHandler;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Application\DTO\Item\ItemDTO;
use Ramsey\Uuid\Uuid;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationController extends AbstractGenericController
{

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
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // create new session
        $session = new Container('MLA_FORM');

        $prg = $this->prg('/inventory/item/create', true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $hasFormToken = $session->offsetExists('form_token');

            if (! $hasFormToken) {
                $tk = Uuid::uuid4()->toString();
                $session->offsetSet('form_token', $tk);
            } else {
                $tk = $session->offsetGet('form_token');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/item/create",
                'form_title' => "Create Item",
                'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                'form_token' => $tk
            ));

            $viewModel->setTemplate("inventory/item/crud");
            return $viewModel;
        }

        $data = $prg;

        $form_token = $data['form_token'];

        $tk = $session->offsetGet('form_token');

        if ($form_token != $tk) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));
        $dto = ItemAssembler::createItemDTOFromArray($data);

        $userId = $u->getId();

        $notification = $this->itemCRUDService->create($dto, 1, $userId, __METHOD__, true);
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/item/create",
                'form_title' => "Create Item",
                'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                'form_token' => $tk
            ));

            $viewModel->setTemplate("inventory/item/crud-v1");
            return $viewModel;
        }

        $session->getManager()
            ->getStorage()
            ->clear('MLA_FORM');

        $this->flashMessenger()->addMessage($notification->successMessage(false) . '\n' . $tk);
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
    {}
}

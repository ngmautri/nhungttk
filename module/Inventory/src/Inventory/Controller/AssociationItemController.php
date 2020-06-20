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
use MLA\Paginator;
use Ramsey\Uuid\Uuid;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationItemController extends AbstractGenericController
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
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $sort_criteria = array();
        $criteria = array();

        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');

        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $layout = $this->params()->fromQuery('layout');
        $page = $this->params()->fromQuery('page');
        $resultsPerPage = $this->params()->fromQuery('perPage');

        $criteria1 = array();
        if (! $item_type == null) {
            $criteria1 = array(
                "itemType" => $item_type
            );
        }

        if ($is_active == null) {
            $is_active = 1;
        }

        $criteria2 = array();

        if ($is_active == 1) {
            $criteria2 = array(
                "isActive" => 1
            );
        } elseif ($is_active == - 1) {
            $criteria2 = array(
                "isActive" => 0
            );
        }

        $criteria3 = array();
        if (! $is_fixed_asset == '') {
            $criteria3 = array(
                "isFixedAsset" => $is_fixed_asset
            );

            if ($is_fixed_asset == - 1) {
                $criteria3 = array(
                    "isFixedAsset" => 0
                );
            }
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        if ($layout == null) :
            $layout = "grid";
        endif;

        $sort_criteria = array(
            $sort_by => $sort
        );

        $criteria = array_merge($criteria1, $criteria2, $criteria3);
        // var_dump($criteria);

        if ($resultsPerPage == null) {
            $resultsPerPage = 28;
        }

        if ($page == null) {
            $page = 1;
        }
        ;

        $res = $this->getItemListRepository();

        $total_recored_cache_key = "item_list_type" . $item_type . "_is_active" . $is_active . "_is_fixed_asset" . $is_fixed_asset;

        $ck = $this->cacheService->hasItem($total_recored_cache_key);

        if ($ck) {
            $total_records = $this->cacheService->getItem($total_recored_cache_key);
        } else {
            $total_records = $res->getTotalItem($item_type, $is_active, $is_fixed_asset);
            $this->cacheService->setItem($total_recored_cache_key, $total_records);
        }

        $paginator = null;
        $list = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        } else {
            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        }

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'perPage' => $resultsPerPage,
            'item_type' => $item_type,
            'layout' => $layout,
            'nmtPlugin' => $nmtPlugin,
            'page' => $page
        ));

        $viewModel->setTemplate("inventory/item/list2");

        // echo Uuid::uuid4();

        if ($layout == "grid") {
            $viewModel->setTemplate("inventory/item/list-gird3");
        }
        return $viewModel;
    }
}

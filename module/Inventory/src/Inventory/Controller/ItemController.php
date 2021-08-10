<?php
namespace Inventory\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Util\FileExtension;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemCategoryMember;
use Application\Entity\NmtInventoryItemDepartment;
use Application\Entity\NmtInventoryItemPicture;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Item\CreateCmdHandler;
use Inventory\Application\Command\Item\UpdateCmdHandler;
use Inventory\Application\Command\Item\UpdateLogisticDataCmdHandler;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\Service\Item\ItemService;
use Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload;
use Inventory\Infrastructure\Persistence\DoctrineItemListRepository;
use Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository;
use Zend\Barcode\Barcode;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Headers;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemController extends AbstractGenericController
{

    protected $itemListRepository;

    protected $itemReportingRepository;

    protected $itemReportService;

    protected $itemCRUDService;

    protected $itemService;

    protected $itemUploadService;

    const BASE_URL = '/inventory/item/%s';

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createFromFileAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/inventory/item/create-from-file";

        $form_title = "Upload";
        $action = FormActions::UPLOAD;
        $viewTemplete = "/inventory/item/create-from-file";

        $request = $this->getRequest();

        if ($request->isPost()) {

            $notify = new Notification();

            try {

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

                if ($notify->hasErrors()) {
                    $viewModel = new ViewModel(array(
                        'errors' => $notify->getErrors(),
                        'redirectUrl' => null,
                        'nmtPlugin' => $nmtPlugin,
                        'form_action' => $form_action,
                        'form_title' => $form_title,
                        'action' => $action,
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

                $uploader = $this->getItemUploadService();
                $uploader->setCompanyId($this->getCompanyId());
                $uploader->setUserId($this->getUserId());
                $uploader->run("$folder/$file_name");
            } catch (\Exception $e) {
                $this->logException($e, false);
                $notify->addError($e->getMessage());

                $viewModel = new ViewModel(array(
                    'errors' => $notify->getErrors(),
                    'redirectUrl' => null,
                    'nmtPlugin' => $nmtPlugin,
                    'form_action' => $form_action,
                    'form_title' => $form_title,
                    'action' => $action,
                    'companyVO' => $this->getCompanyVO()
                ));
                $viewModel->setTemplate($viewTemplete);
                return $viewModel;
            }

            $this->logInfo(\sprintf('Items uploaded!, (%s-%s)', $file_name, $file_size));
            $this->flashMessenger()->addMessage("uploaded");
            $redirectUrl = '/inventory/item/list2';
            \unlink("$folder/$file_name");
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO Posting
        // =========================

        $viewModel = new ViewModel(array(
            'errors' => null,
            'redirectUrl' => null,
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'action' => $action
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    public function getTemplateAction()
    {
        $f = ROOT.'/public/templates/ItemUpload.xlsx';
        $output = file_get_contents($f);
        $response = $this->getResponse();
        $headers = new Headers();
        $headers->addHeaderLine('Content-Type: xlsx');
        $headers->addHeaderLine('Content-Disposition: attachment; filename="ItemUpload.xlsx"');
        $headers->addHeaderLine('Content-Description: File Transfer');
        $headers->addHeaderLine('Content-Transfer-Encoding: binary');
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $response->setHeaders($headers);
        $response->setContent($output);
        return $response;
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
        $tab_idx = $this->params()->fromQuery('tab_idx');

        $dto = $this->getItemService()->getItemSnapshotByToken($id);
        if ($dto == null) {
            return $this->redirect()->toRoute('not_found');
        }
        // $dto = $rootEntity->makeSnapshot();
        // var_dump($dto);

        $viewModel = new ViewModel(array(
            'action' => Constants::FORM_ACTION_SHOW,
            'form_action' => $action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'dto' => $dto,
            'dto1' => $dto,
            'errors' => null,
            'version' => $dto->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'tab_id' => __FUNCTION__,
            'tab_idx' => $tab_idx,
            'sharedCollection' => $this->getSharedCollection()
        ));
        $viewModel->setTemplate($viewTemplete);

        $this->getLogger()->info(\sprintf("Item #%s viewed by #%s", $id, $u->getId()));
        return $viewModel;
    }

    public function show3Action()
    {
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
        $viewTemplete = "inventory/item/view-v1";

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');

        $dto = $this->getItemService()->getItemSnapshotByToken($id);
        if ($dto == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => Constants::FORM_ACTION_SHOW,
            'form_action' => $action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'dto' => $dto,
            'dto1' => $dto,
            'errors' => null,
            'version' => $dto->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'tab_id' => __FUNCTION__,
            'sharedCollection' => $this->getSharedCollection()
        ));
        $viewModel->setTemplate($viewTemplete);
        $this->getLogger()->info(\sprintf("AP #%s viewed by #%s", $id, $this->getUserId()));
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function show1Action()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $u = $this->getUser();
        $request = $this->getRequest();

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "";
        $form_title = "Show item:";
        $action = Constants::FORM_ACTION_SHOW;
        $viewTemplete = "inventory/item/crud-v1";

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $rootEntity = $this->getItemService()->getDocDetailsByTokenId($id, $token);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $dto = $rootEntity->makeSnapshot();

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'dto' => $dto,
            'dto1' => $dto,
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'tab_id' => __FUNCTION__,
            'sharedCollection' => $this->getSharedCollection()
        ));
        $viewModel->setTemplate($viewTemplete);

        $this->getLogger()->info(\sprintf("Item #%s viewed by #%s", $id, $u->getId()));
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

        $viewTemplete = \sprintf(self::BASE_URL, 'crud-v1');
        $action = Constants::FORM_ACTION_ADD;
        $form_action = \sprintf(self::BASE_URL, 'create');
        $form_title = $nmtPlugin->translate("Create Item");
        $prg = $this->prg('/inventory/item/create', true);

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
                'dto' => null,
                'dto1' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'tab_id' => __FUNCTION__,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = new Notification();
        try {
            $data = $prg;

            /**
             *
             * @var ItemDTO $dto ;
             */
            $dto = DTOFactory::createDTOFromArray($data, new ItemDTO());

            $options = new CreateItemOptions($this->getCompanyId(), $this->getUserId(), __METHOD__);
            $cmdHandler = new CreateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();

            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'action' => $action,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'dto' => null,
                'dto1' => null,
                'nmtPlugin' => $nmtPlugin,
                'tab_id' => __FUNCTION__
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
            $dto = $rootEntity->makeSnapshot();
            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto' => $dto,
                'dto1' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'version' => $rootEntity->getRevisionNo(),
                'tab_id' => __FUNCTION__,
                'sharedCollection' => $this->getSharedCollection()
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
            // var_dump($data);

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
            $this->logInfo(\sprintf("Item update %s", $entity_id));
        } catch (\Exception $e) {
            $this->logException($e);
            $notification->addError($e->getMessage());
        }
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => $redirectUrl,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto' => $dto,
                'dto1' => $rootEntity->makeSnapshot(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'version' => $rootEntity->getRevisionNo(),
                'tab_id' => __FUNCTION__,
                'sharedCollection' => $this->getSharedCollection()
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
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateLogisticAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $u = $this->getUser();

        $nmtPlugin = $this->Nmtplugin();
        $prg = $this->prg('/inventory/item/update-logistic', true);
        $form_action = "/inventory/item/update-logistic";
        $form_title = "Edit Logistic Data";
        $viewTemplate = "inventory/item/crud-v1";

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
                'dto1' => $rootEntity->makeSnapshot(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'version' => $rootEntity->getRevisionNo(),
                'tab_id' => __FUNCTION__,
                'tab_idx' => 1,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $this->getLogger()->info(\sprintf("Item Logistic Data will be updated %s", $entity_id));

            $viewModel->setTemplate($viewTemplate);
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
            // var_dump($data);

            $dto1 = DTOFactory::createDTOFromArray($data, new ItemDTO());
            $rootEntity = $this->getItemService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            $options = new UpdateItemOptions($rootEntity, $entity_id, $entity_token, $version, $u->getId(), __METHOD__);

            $cmdHandler = new UpdateLogisticDataCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto1, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $dto1->getNotification();
            $this->getLogger()->info(\sprintf("Item Logistic Data updated %s", $entity_id));
        } catch (\Exception $e) {
            $this->getLogger()->debug($e->getMessage());
            $notification->addError($e->getMessage());
        }
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => $redirectUrl,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto1' => $dto1,
                'dto' => $rootEntity->makeSnapshot(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => Constants::FORM_ACTION_EDIT,
                'version' => $rootEntity->getRevisionNo(),
                'tab_id' => __FUNCTION__,
                'tab_idx' => 1
            ));

            $viewModel->setTemplate($viewTemplate);
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
    public function list2Action()
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
        $limit = null;
        $offset = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, $limit, $offset);

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

    // ===========================
    // Deprecated.
    // ===========================

    /**
     *
     * @deprecated
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function update1Action()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $u = $this->getUser();
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg('/inventory/item/update', true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {

            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('token');
            $dto = $this->itemCRUDService->show($entity_id, $token);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/item/update",
                'form_title' => "Edit Item",
                'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                'n' => 0,
                'tab_id' => __FUNCTION__
            ));

            $viewModel->setTemplate("inventory/item/crud-v1");
            return $viewModel;
        }

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $data = $prg;

        $redirectUrl = $data['redirectUrl'];
        $entity_id = (int) $data['entity_id'];
        $nTry = $data['n'];

        $dto = ItemAssembler::createItemDTOFromArray($data);

        $userId = $u->getId();

        $notification = $this->itemCRUDService->update($entity_id, $dto, $userId, __METHOD__);
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->errorMessage(),
                'redirectUrl' => $redirectUrl,
                'entity_id' => $entity_id,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/item/update",
                'form_title' => "Edit Item",
                'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                'n' => $nTry
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
     * @deprecated
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getItemAction()
    {
        $id = $this->params()->fromQuery('id');

        /**@var \Application\Entity\NmtInventoryItem $item ;*/
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy(array(
            "id" => $id
        ));

        $a_json_row = array();

        if ($item != null) {
            $a_json_row["id"] = $item->getId();
            $a_json_row["token"] = $item->getToken();

            $uom_code = '';
            $purchase_uom_code = '';

            if ($item->getStandardUom() != null) {
                $uom_code = $item->getStandardUom()->getUomCode();
                $purchase_uom_code = $uom_code;
            }

            $a_json_row["uom_code"] = $uom_code;

            if ($item->getPurchaseUom() != null) {
                $purchase_uom_code = $item->getPurchaseUom()->getUomCode();
            }

            $a_json_row["purchase_uom_code"] = $purchase_uom_code;

            $purchaseCF = 1;
            if ($item->getPurchaseUomConvertFactor() != null) {
                $purchaseCF = $item->getPurchaseUomConvertFactor();
            }
            $a_json_row["purchase_uom_convert_factor"] = $purchaseCF;
        }
        // var_dump($a_json);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_row));
        return $response;
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function showAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $item_group_list = $nmtPlugin->itemGroupList();
        $uom_list = $nmtPlugin->uomList();

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $tab_idx = (int) $this->params()->fromQuery('tab_idx');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $item = $res->getItem($entity_id, $token);

        if ($item == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $entity = null;
        if ($item[0] instanceof NmtInventoryItem) {
            $entity = $item[0];
        }

        // $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        $pictures = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy(array(
            "item" => $entity_id
        ));

        if ($entity instanceof NmtInventoryItem) {
            return new ViewModel(array(
                'entity' => $entity,
                'department' => null,
                'category' => null,
                'pictures' => $pictures,
                'redirectUrl' => $redirectUrl,
                'item_group_list' => $item_group_list,
                'uom_list' => $uom_list,

                'total_picture' => $item['total_picture'],
                'total_attachment' => $item['total_attachment'],
                'total_pr_row' => $item['total_pr_row'],
                'total_ap_row' => $item['total_ap_row'],
                'total_po_row' => $item['total_po_row'],
                'total_qo_row' => $item['total_qo_row'],
                'nmtPlugin' => $nmtPlugin,
                'tab_idx' => $tab_idx
            ));
        }

        return $this->redirect()->toRoute('not_found');
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function show2Action()
    {
        $request = $this->getRequest();
        $redirectUrl = null;

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $item_group_list = $nmtPlugin->itemGroupList();
        $uom_list = $nmtPlugin->uomList();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;

        $this->layout("layout/user/ajax");
        $request = $this->getRequest();

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $tab_idx = (int) $this->params()->fromQuery('tab_idx');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $item = $res->getItem($entity_id, $token);

        if ($item == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($item[0] instanceof NmtInventoryItem) {
            $entity = $item[0];
        }

        $pictures = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy(array(
            "item" => $entity_id
        ));

        $onhand_list = $this->itemReportService->getOnhandReportByItem($entity, $u, __METHOD__);

        if (! $entity instanceof NmtInventoryItem) {

            return $this->redirect()->toRoute('access_denied');
        }
        return new ViewModel(array(
            'entity' => $entity,
            'department' => null,
            'category' => null,
            'pictures' => $pictures,
            'redirectUrl' => $redirectUrl,
            'total_picture' => $item['total_picture'],
            'item_group_list' => $item_group_list,
            'uom_list' => $uom_list,

            'total_attachment' => $item['total_attachment'],
            'total_pr_row' => $item['total_pr_row'],
            'total_ap_row' => $item['total_ap_row'],
            'total_po_row' => $item['total_po_row'],
            'total_qo_row' => $item['total_qo_row'],
            'tab_idx' => $tab_idx,
            'onhand_list' => $onhand_list,
            'nmtPlugin' => $nmtPlugin
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $item_group_list = $nmtPlugin->itemGroupList();
        $gl_list = $nmtPlugin->glAccountList();
        $uom_list = $nmtPlugin->uomList();

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $itemSku = $request->getPost('itemSku');
            $itemSku1 = $request->getPost('itemSku1');
            $itemSku2 = $request->getPost('itemSku2');

            $itemName = $request->getPost('itemName');
            $itemNameForeign = $request->getPost('itemNameForeign');
            $itemDescription = $request->getPost('itemDescription');

            $barcode = $request->getPost('barcode');
            $keywords = $request->getPost('keywords');

            $itemType = $request->getPost('itemType');
            $monitoredBy = $request->getPost('monitoredBy');
            if ($monitoredBy == '') {
                $monitoredBy = null;
            }

            $item_category_id = $request->getPost('item_category_id');
            $department_id = $request->getPost('department_id');

            $uom_id = (int) $request->getPost('uom_id');
            $stock_uom_id = (int) $request->getPost('stock_uom_id');

            $purchase_uom_id = (int) $request->getPost('purchase_uom_id');
            $sales_uom_id = (int) $request->getPost('sales_uom_id');

            $stockUomConvertFactor = $request->getPost('stockUomConvertFactor');
            $purchaseUomConvertFactor = $request->getPost('purchaseUomConvertFactor');
            $salesUomConvertFactor = $request->getPost('salesUomConvertFactor');

            $leadTime = $request->getPost('leadTime');
            $assetLabel = $request->getPost('assetLabel');

            $isActive = (int) $request->getPost('isActive');
            $isFixedAsset = (int) $request->getPost('isFixedAsset');
            $isPurchased = (int) $request->getPost('isPurchased');
            $isSaleItem = (int) $request->getPost('isSaleItem');
            $isSparepart = (int) $request->getPost('isSparepart');
            $isStocked = (int) $request->getPost('isStocked');
            $item_group_id = (int) $request->getPost('item_group_id');

            $manufacturer = $request->getPost('manufacturer');
            $manufacturerCatalog = $request->getPost('manufacturerCatalog');
            $manufacturerCode = $request->getPost('manufacturerCode');
            $manufacturerModel = $request->getPost('manufacturerModel');
            $manufacturerSerial = $request->getPost('manufacturerSerial');

            $location = $request->getPost('location');
            $localAvailabiliy = (int) $request->getPost('localAvailabiliy');

            $remarks = $request->getPost('remarksText');

            // Create NEW ITEM
            $entity = new NmtInventoryItem();

            if ($itemSku === '' or $itemSku === null) {
                $errors[] = 'Please give Item ID';
            } else {
                $entity->setItemSku($itemSku);
            }

            $entity->setItemSku1($itemSku1);
            $entity->setItemSku2($itemSku2);

            if ($itemName === '' or $itemName === null) {
                $errors[] = 'Please give item name';
            } else {
                $entity->setItemName($itemName);
            }

            if ($isActive !== 1) {
                $isActive = 0;
            }

            if ($isFixedAsset !== 1) {
                $isFixedAsset = 0;
            }
            if ($isPurchased !== 1) {
                $isPurchased = 0;
            }
            if ($isSaleItem !== 1) {
                $isSaleItem = 0;
            }

            if ($isSparepart !== 1) {
                $isSparepart = 0;
            }

            if ($isStocked !== 1) {
                $isStocked = 0;
            }

            if ($localAvailabiliy !== 1) {
                $localAvailabiliy = 0;
            }
            $entity->setItemNameForeign($itemNameForeign);
            $entity->setItemDescription($itemDescription);
            $entity->setKeywords($keywords);
            $entity->setBarcode($barcode);
            $entity->setCompany();

            $entity->setIsActive($isActive);
            $entity->setIsFixedAsset($isFixedAsset);
            $entity->setIsPurchased($isPurchased);
            $entity->setIsSaleItem($isSaleItem);
            $entity->setIsSparepart($isSparepart);
            $entity->setIsStocked($isStocked);

            // $entity->setItemGroup ( $itemGroup );
            // $entity->setItemInternalLabel ( $itemInternalLabel );

            $entity->setItemType($itemType);
            $entity->setMonitoredBy($monitoredBy);

            $entity->setManufacturer($manufacturer);
            $entity->setManufacturerCatalog($manufacturerCatalog);
            $entity->setManufacturerCode($manufacturerCode);
            $entity->setManufacturerModel($manufacturerModel);
            $entity->setManufacturerSerial($manufacturerSerial);

            $entity->setAssetLabel($assetLabel);

            // $entity->setOrigin($origin);
            // $entity->setSerialNumber($serialNumber);

            if ($uom_id == 0 or $uom_id == null) {
                $errors[] = 'Please give standard measurement!';
            } else {
                $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $uom_id);

                if ($uom !== null) {
                    $entity->setStandardUom($uom);
                } else {
                    $errors[] = 'Please give standard measurement!';
                }
            }

            if ($stock_uom_id == 0 or $uom_id == null) {
                $errors[] = 'Please give stock measurement!';
            } else {
                $stock_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $stock_uom_id);

                if ($stock_uom !== null) {
                    $entity->setStockUom($stock_uom);
                } else {
                    $errors[] = 'Please give stock measurement!';
                }
            }

            if ($stockUomConvertFactor != null) {

                if (! is_numeric($stockUomConvertFactor)) {
                    $errors[] = $nmtPlugin->translate('Please give conversion factor');
                } else {

                    if ($stockUomConvertFactor <= 0) {
                        $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                    } else {
                        $entity->setStockUomConvertFactor($stockUomConvertFactor);
                    }
                }
            }

            $n_validated = 0;
            if ($purchase_uom_id != 0 or $purchase_uom_id != null) {

                $purchase_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $purchase_uom_id);

                if ($purchase_uom !== null) {
                    $entity->setPurchaseUom($purchase_uom);
                    $n_validated ++;
                }
            }

            if ($purchaseUomConvertFactor != null) {

                if (! is_numeric($purchaseUomConvertFactor)) {
                    $errors[] = $nmtPlugin->translate('Please give conversion factor');
                } else {

                    if ($purchaseUomConvertFactor <= 0) {
                        $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                    } else {
                        $entity->setPurchaseUomConvertFactor($purchaseUomConvertFactor);
                    }
                }
            }

            $n_validated = 0;
            if ($purchase_uom_id != 0 or $purchase_uom_id != null) {

                $purchase_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $purchase_uom_id);

                if ($purchase_uom !== null) {
                    $entity->setPurchaseUom($purchase_uom);
                    $n_validated ++;
                }
            }

            if ($purchaseUomConvertFactor != null) {

                if (! is_numeric($purchaseUomConvertFactor)) {
                    $errors[] = $nmtPlugin->translate('Please give conversion factor');
                } else {

                    if ($purchaseUomConvertFactor <= 0) {
                        $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                    } else {
                        $entity->setPurchaseUomConvertFactor($purchaseUomConvertFactor);
                    }
                }
            }

            $n_validated = 0;
            if ($sales_uom_id != 0 or $sales_uom_id != null) {

                $sales_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $sales_uom_id);

                if ($sales_uom !== null) {
                    $entity->setSalesUom($sales_uom);
                    $n_validated ++;
                }
            }

            if ($salesUomConvertFactor != null) {
                if (! is_numeric($salesUomConvertFactor)) {
                    $errors[] = $nmtPlugin->translate('Please give conversion factor');
                } else {

                    if ($salesUomConvertFactor <= 0) {
                        $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                    } else {
                        $entity->setSalesUomConvertFactor($salesUomConvertFactor);
                    }
                }
            }

            $item_group = null;
            if ($item_group_id > 0) {
                $item_group = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->find($item_group_id);
                $entity->setItemGroup($item_group);
            }

            $department = null;
            if ($department_id > 0) {
                $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
            }

            $category = null;
            if ($item_category_id > 0) {
                $category = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
            }
            // $entity->setStatus($status);
            // $entity->setValidFromDate($validFromDate);
            // $entity->setValidToDate($validToDate);
            // $entity->setWarehouse ();

            $entity->setRemarksText($remarks);
            $entity->setLocation($location);
            $entity->setLocalAvailabiliy($localAvailabiliy);
            $entity->setLeadTime($leadTime);

            $company_id = 1;
            $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
            $entity->setCompany($company);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'department' => $department,
                    'category' => $category,
                    'item_group_list' => $item_group_list,
                    'uom_list' => $uom_list
                ));
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {

                // Assign doc number
                $entity->setSysNumber($nmtPlugin->getDocNumber($entity));

                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));

                $createdOn = new \DateTime();

                $entity->setCreatedOn(new \DateTime());
                $entity->setCreatedBy($u);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $new_id = $entity->getId();

                $new_item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $new_id);
                $entity->setChecksum(md5($new_id . uniqid(microtime())));
                $this->doctrineEM->flush();

                // add category member
                if ($item_category_id > 0) {

                    $itemCategory = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
                    $entity = new NmtInventoryItemCategoryMember();
                    $entity->setItem($new_item);
                    $entity->setCategory($itemCategory);

                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn($createdOn);

                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();
                }

                // add department member
                if ($department_id > 0) {
                    $entity = new NmtInventoryItemDepartment();

                    $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                    $entity->setDepartment($department);
                    $entity->setItem($new_item);

                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn($createdOn);

                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();
                }

                // update search index.
                // $this->itemSearchService->addDocument ( $new_item, true );
                $indexing_log = $this->itemSearchService->updateItemIndex(1, $new_item, false);

                $m = sprintf('[OK] (%s) #%s %s created.<br> %s', $itemName, $new_item->getId(), $new_item->getSysNumber(), $indexing_log);

                // Trigger. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $createdOn
                ));

                $this->flashMessenger()->addMessage($m);
                $redirectUrl = "/inventory/item/show?token=" . $new_item->getToken() . "&entity_id=" . $new_item->getId() . "&checksum=" . $new_item->getChecksum();
                $this->redirect()->toUrl($redirectUrl);
            } catch (Exception $e) {

                $errors[] = $e->getMessage();
                return new ViewModel(array(
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'department' => $department,
                    'category' => $category,
                    'item_group_list' => $item_group_list,
                    'uom_list' => $uom_list
                ));
            }
        }

        // NO POST
        // Initiate ......................
        // =====================================================
        $redirectUrl = null;
        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * } else {
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         * }
         */
        return new ViewModel(array(
            'errors' => null,
            'redirectUrl' => $redirectUrl,
            'entity' => null,
            'department' => null,
            'category' => null,
            'item_group_list' => $item_group_list,
            'uom_list' => $uom_list
        ));
    }

    /**
     * * @deprecated
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $item_group_list = $nmtPlugin->itemGroupList();
        $gl_list = $nmtPlugin->glAccountList();
        $uom_list = $nmtPlugin->uomList();

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtInventoryItem $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'department' => null,
                    'category' => null,
                    'n' => $nTry,
                    'item_group_list' => $item_group_list,
                    'uom_list' => $uom_list
                ));

                // might need redirect
            } else {

                $oldEntity = clone ($entity);

                $itemSku = $request->getPost('itemSku');
                $itemSku1 = $request->getPost('itemSku1');
                $itemSku2 = $request->getPost('itemSku2');

                $itemName = $request->getPost('itemName');
                $itemNameForeign = $request->getPost('itemNameForeign');
                $itemDescription = $request->getPost('itemDescription');

                $barcode = $request->getPost('barcode');
                $keywords = $request->getPost('keywords');

                $itemType = $request->getPost('itemType');
                $monitoredBy = $request->getPost('monitoredBy');
                if ($monitoredBy == '') {
                    $monitoredBy = null;
                }

                $item_category_id = $request->getPost('item_category_id');
                $department_id = $request->getPost('department_id');

                $uom_id = (int) $request->getPost('uom_id');
                $stock_uom_id = (int) $request->getPost('stock_uom_id');

                $purchase_uom_id = (int) $request->getPost('purchase_uom_id');
                $sales_uom_id = (int) $request->getPost('sales_uom_id');

                $stockUomConvertFactor = $request->getPost('stockUomConvertFactor');
                $purchaseUomConvertFactor = $request->getPost('purchaseUomConvertFactor');
                $salesUomConvertFactor = $request->getPost('salesUomConvertFactor');

                $leadTime = $request->getPost('leadTime');

                $isActive = (int) $request->getPost('isActive');
                $isFixedAsset = (int) $request->getPost('isFixedAsset');
                $isPurchased = (int) $request->getPost('isPurchased');
                $isSaleItem = (int) $request->getPost('isSaleItem');
                $isSparepart = (int) $request->getPost('isSparepart');
                $isStocked = (int) $request->getPost('isStocked');
                $item_group_id = (int) $request->getPost('item_group_id');

                $assetLabel = $request->getPost('assetLabel');

                $manufacturer = $request->getPost('manufacturer');
                $manufacturerCatalog = $request->getPost('manufacturerCatalog');
                $manufacturerCode = $request->getPost('manufacturerCode');
                $manufacturerModel = $request->getPost('manufacturerModel');
                $manufacturerSerial = $request->getPost('manufacturerSerial');

                $location = $request->getPost('location');
                $localAvailabiliy = (int) $request->getPost('localAvailabiliy');

                $remarks = $request->getPost('remarksText');

                if ($itemSku === '' or $itemSku === null) {
                    $errors[] = 'Please give Item ID';
                } else {
                    $entity->setItemSku($itemSku);
                }

                $entity->setItemSku1($itemSku1);
                $entity->setItemSku2($itemSku2);

                if ($itemName === '' or $itemName === null) {
                    $errors[] = 'Please give item name';
                } else {
                    $entity->setItemName($itemName);
                }

                if ($isActive !== 1) {
                    $isActive = 0;
                }

                if ($isFixedAsset !== 1) {
                    $isFixedAsset = 0;
                }
                if ($isPurchased !== 1) {
                    $isPurchased = 0;
                }
                if ($isSaleItem !== 1) {
                    $isSaleItem = 0;
                }

                if ($isSparepart !== 1) {
                    $isSparepart = 0;
                }

                if ($isStocked !== 1) {
                    $isStocked = 0;
                }

                if ($localAvailabiliy !== 1) {
                    $localAvailabiliy = 0;
                }

                $entity->setItemNameForeign($itemNameForeign);
                $entity->setItemDescription($itemDescription);
                $entity->setKeywords($keywords);
                $entity->setBarcode($barcode);
                $entity->setCompany();

                $entity->setIsActive($isActive);
                $entity->setIsFixedAsset($isFixedAsset);
                $entity->setIsPurchased($isPurchased);
                $entity->setIsSaleItem($isSaleItem);
                $entity->setIsSparepart($isSparepart);
                $entity->setIsStocked($isStocked);

                // $entity->setItemGroup ( $itemGroup );
                // $entity->setItemInternalLabel ( $itemInternalLabel );

                $entity->setItemType($itemType);
                $entity->setMonitoredBy($monitoredBy);

                $entity->setManufacturer($manufacturer);
                $entity->setManufacturerCatalog($manufacturerCatalog);
                $entity->setManufacturerCode($manufacturerCode);
                $entity->setManufacturerModel($manufacturerModel);
                $entity->setManufacturerSerial($manufacturerSerial);

                $entity->setAssetLabel($assetLabel);

                // $entity->setOrigin($origin);
                // $entity->setSerialNumber($serialNumber);

                if ($uom_id == 0 or $uom_id == null) {
                    $errors[] = 'Please give standard measurement!';
                } else {
                    $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $uom_id);

                    if ($uom !== null) {
                        $entity->setStandardUom($uom);
                    } else {
                        $errors[] = 'Please give standard measurement!';
                    }
                }

                if ($stock_uom_id == 0 or $uom_id == null) {
                    $errors[] = 'Please give standard measurement!';
                } else {
                    $stock_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $stock_uom_id);

                    if ($stock_uom !== null) {
                        $entity->setStockUom($stock_uom);
                    } else {
                        $errors[] = 'Please give stock measurement!';
                    }
                }

                if ($stockUomConvertFactor != null) {

                    if (! is_numeric($stockUomConvertFactor)) {
                        $errors[] = $nmtPlugin->translate('Please give conversion factor');
                    } else {

                        if ($stockUomConvertFactor <= 0) {
                            $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                        } else {
                            $entity->setStockUomConvertFactor($stockUomConvertFactor);
                        }
                    }
                }

                $n_validated = 0;
                if ($purchase_uom_id != 0 or $purchase_uom_id != null) {

                    $purchase_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $purchase_uom_id);

                    if ($purchase_uom !== null) {
                        $entity->setPurchaseUom($purchase_uom);
                        $n_validated ++;
                    }
                }

                if ($purchaseUomConvertFactor != null) {

                    if (! is_numeric($purchaseUomConvertFactor)) {
                        $errors[] = $nmtPlugin->translate('Please give conversion factor');
                    } else {

                        if ($purchaseUomConvertFactor <= 0) {
                            $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                        } else {
                            $entity->setPurchaseUomConvertFactor($purchaseUomConvertFactor);
                        }
                    }
                }

                $n_validated = 0;
                if ($sales_uom_id != 0 or $sales_uom_id != null) {

                    $sales_uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $sales_uom_id);

                    if ($sales_uom !== null) {
                        $entity->setSalesUom($sales_uom);
                        $n_validated ++;
                    }
                }

                if ($salesUomConvertFactor != null) {
                    if (! is_numeric($salesUomConvertFactor)) {
                        $errors[] = $nmtPlugin->translate('Please give conversion factor');
                    } else {

                        if ($salesUomConvertFactor <= 0) {
                            $errors[] = $nmtPlugin->translate('Conversion Factor must be greater than 0!');
                        } else {
                            $entity->setSalesUomConvertFactor($salesUomConvertFactor);
                        }
                    }
                }

                $item_group = null;
                if ($item_group_id > 0) {
                    $item_group = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->find($item_group_id);
                    $entity->setItemGroup($item_group);
                }

                $department = null;
                if ($department_id > 0) {
                    $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                }

                $category = null;
                if ($item_category_id > 0) {
                    $category = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
                }
                // $entity->setStatus($status);
                // $entity->setValidFromDate($validFromDate);
                // $entity->setValidToDate($validToDate);
                // $entity->setWarehouse ();

                $entity->setRemarksText($remarks);
                $entity->setLocation($location);
                $entity->setLocalAvailabiliy($localAvailabiliy);
                $entity->setLeadTime($leadTime);

                $company_id = 1;
                $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
                $entity->setCompany($company);

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }

                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "%s (%s)"?', $entity->getItemName(), $entity->getSysNumber);
                }

                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "%s (%s)". Please try later!', $entity->getItemName(), $entity->getSysNumber());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'entity' => $entity,
                        'department' => $department,
                        'category' => $category,
                        'n' => $nTry,
                        'item_group_list' => $item_group_list,
                        'uom_list' => $uom_list
                    ));
                }

                // NO ERROR
                // ++++++++++++++++++++++++
                try {

                    $changeOn = new \DateTime();

                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        'email' => $this->identity()
                    ));

                    $entity->setRevisionNo($entity->getRevisionNo() + 1);

                    $entity->setLastchangeBy($u);
                    $entity->setLastchangeOn($changeOn);

                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();

                    $new_item = $entity;

                    // add category member
                    if ($item_category_id > 0) {
                        $entity = new NmtInventoryItemCategoryMember();
                        $entity->setItem($new_item);
                        $entity->setCategory($category);

                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());

                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                    }

                    // add department member
                    if ($department_id > 0) {
                        $entity = new NmtInventoryItemDepartment();
                        $entity->setDepartment($department);
                        $entity->setItem($new_item);

                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());

                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                    }

                    // update index
                    // $this->itemSearchService->updateIndex(0, $new_item, false);

                    $indexing_log = $this->itemSearchService->updateItemIndex(0, $entity, false);
                    // $m = sprintf('[OK] (%s) #%s %s created.<br/> %s', $itemName, $new_item->getId(), $new_item->getSysNumber(),$indexing_log);

                    $m = sprintf('[OK] (%s) #%s %s updated. Change no.:%s.<br/> %s', $itemName, $new_item->getId(), $new_item->getSysNumber(), count($changeArray), $indexing_log);

                    // Trigger Change Log. AbtractController is EventManagerAware.
                    $this->getEventManager()->trigger('inventory.change.log', __METHOD__, array(
                        'priority' => \Zend\Log\Logger::INFO,
                        'message' => $m,
                        'objectId' => $new_item->getId(),
                        'objectToken' => $new_item->getToken(),
                        'changeArray' => $changeArray,
                        'changeBy' => $u,
                        'changeOn' => $changeOn,
                        'revisionNumber' => $new_item->getRevisionNo(),
                        'changeDate' => $changeOn,
                        'changeValidFrom' => $changeOn
                    ));

                    // Trigger Activity Log . AbtractController is EventManagerAware.
                    $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                        'priority' => \Zend\Log\Logger::INFO,
                        'message' => $m,
                        'createdBy' => $u,
                        'createdOn' => $changeOn
                    ));

                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                } catch (Exception $e) {

                    $errors[] = $e->getMessage();

                    return new ViewModel(array(
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'entity' => $new_item,
                        'department' => $department,
                        'category' => $category,
                        'n' => $nTry,
                        'item_group_list' => $item_group_list,
                        'uom_list' => $uom_list
                    ));
                }
            }
        }

        // NO POST
        // ==================================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if (! $entity == null) {
            return new ViewModel(array(
                'errors' => null,
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'category' => null,
                'department' => null,
                'n' => 0,
                'item_group_list' => $item_group_list,
                'uom_list' => $uom_list
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @deprecated
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

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 28;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        }
        ;

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');

        $total_recored_cache_key = "item_list_type" . $item_type . "_is_active" . $is_active . "_is_fixed_asset" . $is_fixed_asset;

        $ck = $this->cacheService->hasItem($total_recored_cache_key);

        if ($ck) {
            $total_records = $this->cacheService->getItem($total_recored_cache_key);
        } else {
            $total_records = $res->getTotalItem($item_type, $is_active, $is_fixed_asset);
            $this->cacheService->setItem($total_recored_cache_key, $total_records);
        }

        // $serializer = new \Zend\Serializer\Adapter\PhpSerialize();

        // $list = $res->getVendorInvoiceList($is_active,$currentState,null,$sort_by,$sort,0,0);
        // $total_records = count($list);

        // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria );
        // $total_records = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getTotalItem($item_type, $is_active, $is_fixed_asset);
        // echo($total_records);

        // $total_records =count($list);

        /*
         * try {
         *
         * $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
         *
         * $serialized = $serializer->serialize($list);
         * // now $serialized is a string
         * //$this->cacheService->setItem("list_object", $serialized);
         *
         * // now $data == $unserialized
         * } catch (\Zend\Serializer\Exception\ExceptionInterface $e) {
         * echo $e;
         * }
         */

        $paginator = null;
        $list = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
            // $list = array_slice($list, $paginator->getMinInPage() - 1, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1);

            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        }

        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type,
            'layout' => $layout,
            'nmtPlugin' => $nmtPlugin,
            'page' => $page
        ));

        // echo Uuid::uuid4();

        if ($layout == "grid") {
            $viewModel->setTemplate("inventory/item/list-gird");
        }
        return $viewModel;
    }

    /**
     * * @deprecated
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $sort_criteria = array();
        $criteria = array();

        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');

        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

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

        $sort_criteria = array(
            $sort_by => $sort
        );

        $criteria = array_merge($criteria1, $criteria2, $criteria3);
        // var_dump($criteria);

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 20;
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

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');

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
            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $list = $res->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type
        ));
    }

    /**
     * * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function itemPriceAction()
    {
        $sort_criteria = array();
        $criteria = array();

        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');

        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

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
            $sort = "ASC";
	    endif;

        $sort_criteria = array(
            $sort_by => $sort
        );

        $criteria = array_merge($criteria1, $criteria2, $criteria3);
        // var_dump($criteria);

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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        // $total_records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getTotalItem($item_type, $is_active, $is_fixed_asset);
        // echo($total_records);

        // $total_records = count($list);
        $total_records = 100;

        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
            // $list = array_slice($list, $paginator->getMinInPage() - 1, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        }

        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadPictureAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $pictures = $_POST['pictures'];
            $id = $_POST['target_id'];
            $documentSubject = $_POST['subject'];

            $result = array();
            $success = 0;
            $failed = 0;
            $n = 0;

            foreach ($pictures as $p) {
                $n ++;
                $filetype = $p[0];
                $original_filename = $p[2];

                if (preg_match('/(jpg|jpeg)$/', $filetype)) {
                    $ext = 'jpg';
                } else if (preg_match('/(gif)$/', $filetype)) {
                    $ext = 'gif';
                } else if (preg_match('/(png)$/', $filetype)) {
                    $ext = 'png';
                }

                // fix uix folder.
                $tmp_name = ROOT . "/temp/" . md5($id . uniqid(microtime())) . '.' . $ext;

                /**
                 * Important! for UBUNTU
                 */
                $tmp_name = str_replace('\\', '/', $tmp_name);

                // remove "data:image/png;base64,"
                $uri = substr($p[1], strpos($p[1], ",") + 1);

                // save to file
                file_put_contents($tmp_name, base64_decode($uri));

                $checksum = md5_file($tmp_name);

                $root_dir = ROOT . "/data/inventory/picture/item/";

                $criteria = array(
                    "checksum" => $checksum,
                    "item" => $id
                );

                $ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findby($criteria);

                if (count($ck) == 0) {
                    try {
                        $name_part1 = Rand::getString(6, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true);
                        $name = md5($id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;

                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                        $folder = $root_dir . DIRECTORY_SEPARATOR . $folder_relative;

                        /**
                         * Important! for UBUNTU
                         */
                        $folder = str_replace('\\', '/', $folder);

                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }

                        rename($tmp_name, "$folder/$name");

                        $entity = new NmtInventoryItemPicture();

                        if ($documentSubject == null) {
                            $documentSubject = "Picture for " . $id;
                        }
                        $entity->setDocumentSubject($documentSubject);
                        $entity->setIsActive(1);

                        $entity->setUrl($folder . DIRECTORY_SEPARATOR . $name);
                        $entity->setFiletype($filetype);
                        $entity->setFilename($name);
                        $entity->setOriginalFilename($original_filename);
                        $entity->setFolder($folder);
                        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);

                        $entity->setChecksum($checksum);
                        $entity->setVisibility(1);
                        $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $id);
                        $entity->setItem($item);

                        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                            "email" => $this->identity()
                        ));

                        $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());

                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();

                        // trigger uploadPicture. AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                            'picture_name' => $name,
                            'pictures_dir' => $folder,
                            'relavite_folder' => $folder_relative,
                            'type' => 'ITEM'
                        ));

                        $m = sprintf("[OK] %s uploaded sucessfully!", $original_filename);
                        $this->flashMessenger()->addMessage($m);

                        $result[] = $original_filename . ' uploaded sucessfully';
                        $success ++;
                    } catch (Exception $e) {
                        $this->flashMessenger()->addMessage($e);
                        $result[] = $e;
                        $failed ++;
                    }
                } else {
                    $this->flashMessenger()->addMessage("'" . $original_filename . "' exits already!");
                    $result[] = $original_filename . ' exits already. Please select other file!';
                    $failed ++;
                }
            }
            // $data['filetype'] = $filetype;
            $data = array();
            $data['message'] = $result;
            $data['total_uploaded'] = $n;
            $data['success'] = $success;
            $data['failed'] = $failed;

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            // 'checksum' => $checksum,
            'token' => $token
        );

        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if ($target !== null) {
            return new ViewModel(array(
                'item' => $target,
                'redirectUrl' => $redirectUrl,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getPictureAction()
    {
        // $request = $this->getRequest();

        // accepted only ajax request
        /*
         * if (! $request->isXmlHttpRequest()) {o
         * return $this->redirect()->toRoute('access_denied');
         * }
         */
        $item_id = (int) $this->params()->fromQuery('item_id');

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $item_id,
            'isActive' => 1
        ));

        if ($pic instanceof NmtInventoryItemPicture) {

            // $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_450_" . $pic->getFileName();

            $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            /**
             * Important! for UBUNTU
             */
            $pic_folder = str_replace('\\', '/', $pic_folder);

            $imageContent = file_get_contents($pic_folder);

            $response = $this->getResponse();
            $response->setContent($imageContent);

            $response->getHeaders()
                ->addHeaderLine('Cache-Control', 'max-age=3600, must-revalidate', true)
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
            // ->addHeaderLine('Content-Length', mb_strlen($imageContent)); // can cause problme in Ubuntu
            return $response;
        } else {
            $pic_folder = getcwd() . "/public/images/no-pic1.jpg";
            $imageContent = file_get_contents($pic_folder);

            $response = $this->getResponse();

            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', 'image/jpeg');
            // ->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getPicture1Action()
    {
        $item_id = (int) $this->params()->fromQuery('item_id');

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $item_id,
            'isActive' => 1
        ));

        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "/thumbnail/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU

            return $thumbnail_file;
        }

        return $thumbnail_file;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getPicture2Action()
    {
        $this->layout("layout/user/ajax");

        $item_id = (int) $this->params()->fromQuery('item_id');

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $item_id,
            'isActive' => 1
        ));

        $a_json_row = array();

        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "/thumbnail/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU
        }

        $a_json_row[] = $thumbnail_file;

        // var_dump($a_json);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_row));
        return $response;
    }

    /**
     *
     * @deprecated
     */
    public function barcodeAction()
    {
        $barcode = $this->params()->fromQuery('barcode');

        if ($barcode !== "") {
            // Only the text to draw is required
            $barcodeOptions = array(
                'text' => $barcode
            );

            // No required options
            $rendererOptions = array();

            // Draw the barcode in a new image,
            Barcode::factory('code39', 'image', $barcodeOptions, $rendererOptions)->render();
        } else {
            return;
        }
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();

        // var_dump($criteria);
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("item_" . $entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        // update search index()
        $this->itemSearchService->createItemIndex();

        $total_records = count($list);

        return new ViewModel(array(
            'total_records' => $total_records
        ));
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateLastTransactionAction()
    {

        // Update Last GR
        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $last_gr = $res->getItemLastTrx();

        $total_gr = count($last_gr);

        if ($total_gr > 0) {
            foreach ($last_gr as $gr) {

                /**@var \Application\Entity\NmtInventoryTrx $gr_entity ;*/
                $gr_entity = $gr[0];
                $gr_entity->getItem()->setLastTrxRow($gr_entity);
            }
        }

        $this->doctrineEM->flush();

        // Update Puchasing Data

        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $item_purchasing = $res->getItemPurchasing();

        $total_purchasing = count($item_purchasing);
        if ($total_purchasing > 0) {
            foreach ($item_purchasing as $purchasing) {

                /**@var \Application\Entity\NmtInventoryItemPurchasing $purchasing_entity ;*/
                $purchasing_entity = $purchasing[0];
                $purchasing_entity->getItem()->setLastPurchasing($purchasing_entity);
            }
        }
        $this->doctrineEM->flush();

        return new ViewModel(array(
            'last_gr' => $total_gr,
            'last_purchasing' => $total_purchasing
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function updateSysNumberAction()
    {
        $criteria = array(
            "isActive" => 1
        );

        // var_dump($criteria);
        $sort_criteria = array(
            "createdOn" => "ASC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy($criteria, $sort_criteria);
        $criteria = array(
            'isActive' => 1,
            'subjectClass' => 'Application\Entity\NmtInventoryItem'
        );

        /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
        $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);

        if (count($list) > 0) {

            $current_no = 0;
            foreach ($list as $entity) {

                /** @var \Application\Entity\NmtInventoryItem $entity ; */

                $criteria = array(
                    'isActive' => 1,
                    'subjectClass' => 'Application\Entity\NmtInventoryItem'
                );

                /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
                $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);

                // generate document
                // ==================
                if ($docNumber != null) {
                    $maxLen = strlen($docNumber->getToNumber());
                    $currentDoc = $docNumber->getPrefix();

                    $current_no ++;
                    $currentLen = strlen($current_no);

                    $tmp = "";
                    for ($i = 0; $i < $maxLen - $currentLen; $i ++) {

                        $tmp = $tmp . "0";
                    }

                    $currentDoc = $currentDoc . $tmp . $current_no;
                    $entity->setSysNumber($currentDoc);
                }
            }
        }

        $docNumber->setCurrentNumber($current_no);
        $this->doctrineEM->flush();

        // update search index()
        $this->itemSearchService->createItemIndex();

        $total_records = count($list);

        return new ViewModel(array(
            'total_records' => $total_records
        ));
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updatePictureTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo ONLY TOKEN
                 */
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        $total_records = count($list);
        return new ViewModel(array(
            'total_records' => $total_records
        ));
    }

    // ===========================
    // setter and getter.
    // ===========================

    /**
     *
     * @return mixed
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     *
     * @param mixed $cacheService
     */
    public function setCacheService(StorageInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     *
     * @return \Inventory\Application\Service\Item\ItemCRUDService
     */
    public function getItemCRUDService()
    {
        return $this->itemCRUDService;
    }

    /**
     *
     * @param \Inventory\Application\Service\Item\ItemCRUDService $itemCRUDService
     */
    public function setItemCRUDService($itemCRUDService)
    {
        $this->itemCRUDService = $itemCRUDService;
    }

    /**
     *
     * @return \Inventory\Infrastructure\Persistence\DoctrineItemListRepository
     */
    public function getItemListRepository()
    {
        return $this->itemListRepository;
    }

    /**
     *
     * @param DoctrineItemListRepository $itemListRepository
     */
    public function setItemListRepository(DoctrineItemListRepository $itemListRepository)
    {
        $this->itemListRepository = $itemListRepository;
    }

    /**
     *
     * @return \Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository
     */
    public function getItemReportingRepository()
    {
        return $this->itemReportingRepository;
    }

    /**
     *
     * @param DoctrineItemReportingRepository $itemReportingRepository
     */
    public function setItemReportingRepository(DoctrineItemReportingRepository $itemReportingRepository)
    {
        $this->itemReportingRepository = $itemReportingRepository;
    }

    /**
     *
     * @return mixed
     */
    public function getItemReportService()
    {
        return $this->itemReportService;
    }

    /**
     *
     * @param mixed $itemReportService
     */
    public function setItemReportService($itemReportService)
    {
        $this->itemReportService = $itemReportService;
    }

    /**
     *
     * @return \Inventory\Application\Service\Item\ItemService
     */
    public function getItemService()
    {
        return $this->itemService;
    }

    public function setItemService(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     *
     * @return \Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload
     */
    public function getItemUploadService()
    {
        return $this->itemUploadService;
    }

    /**
     *
     * @param AbstractItemUpload $itemUploadService
     */
    public function setItemUploadService(AbstractItemUpload $itemUploadService)
    {
        $this->itemUploadService = $itemUploadService;
    }
}

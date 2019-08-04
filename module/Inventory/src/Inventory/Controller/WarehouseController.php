<?php
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryWarehouse;
use Ramsey;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Application\Service\Warehouse\WarehouseService;
use Inventory\Application\DTO\Warehouse\WarehouseDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseController extends AbstractActionController
{

    protected $warehouseService;

    protected $warehouseLocationService;

    protected $doctrineEM;

    protected $whService;

    /**
     *
     * @return \Inventory\Service\WarehouseLocationService
     */
    public function getWarehouseLocationService()
    {
        return $this->warehouseLocationService;
    }

    /**
     *
     * @param \Inventory\Service\WarehouseLocationService $warehouseLocationService
     */
    public function setWarehouseLocationService(\Inventory\Service\WarehouseLocationService $warehouseLocationService)
    {
        $this->warehouseLocationService = $warehouseLocationService;
    }

    /**
     */
    public function locationTreeAction()
    {
        $this->layout("layout/user/ajax");
        $id = (int) $this->params()->fromQuery('id');
        $criteria = array(
            'id' => $id
        );

        /** @var \Application\Entity\NmtInventoryWarehouse $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /** @var \Application\Entity\NmtInventoryWarehouse $root ; */
        $root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->findOneBy(array(
            "locationCode" => $entity->getId() . "-ROOT-LOCATION"
        ));

        $this->warehouseLocationService->initCategory();
        $this->warehouseLocationService->updateCategory($root->getId(), 0);
        $jsTree = $this->warehouseLocationService->generateJSTreeNew($root->getId(), false);

        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/user/ajax" );
         * }
         *
         */
        // $jsTree = $this->tree;
        return new ViewModel(array(
            'jsTree' => $jsTree
        ));
    }

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $prg = $this->prg('/inventory/warehouse/create', true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $dto = new WarehouseDTO();
            $dto->whCountry = $u->getCompany()
                ->getCountry()
                ->getId();
            $dto->whStatus = 1;

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/warehouse/create",
                'form_title' => "Create Warehouse",
                'action' => \Application\Model\Constants::FORM_ACTION_ADD
            ));

            $viewModel->setTemplate("inventory/warehouse/crud2");
            return $viewModel;
        }

        $data = $prg;

        $dto = WarehouseDTOAssembler::createDTOFromArray($data);
        $userId = $u->getId();

        $notification = $this->whService->createHeader($dto, $u->getCompany(), $userId, __METHOD__);
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->errorMessage(),
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/warehouse/create",
                'form_title' => "Create Warehouse",
                'action' => \Application\Model\Constants::FORM_ACTION_ADD
            ));

            $viewModel->setTemplate("inventory/warehouse/crud2");
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false) . '\n');
        $redirectUrl = "/inventory/warehouse/list";

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $prg = $this->prg('/inventory/warehouse/update', true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {

            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('token');
            $dto = $this->whService->getHeader($entity_id, $token);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/warehouse/update",
                'form_title' => "Edit Item",
                'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                'n' => 0
            ));

            $viewModel->setTemplate("inventory/warehouse/crud2");
            return $viewModel;
        }

        $data = $prg;

        $redirectUrl = $data['redirectUrl'];
        $entity_id = (int) $data['entity_id'];
        $nTry = $data['n'];

        $dto = WarehouseDTOAssembler::createDTOFromArray($data);

        $userId = $u->getId();

        $notification = $this->whService->updateHeader($entity_id, "", $dto, $userId, __METHOD__);
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->errorMessage(),
                'redirectUrl' => $redirectUrl,
                'entity_id' => $entity_id,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => "/inventory/warehouse/update",
                'form_title' => "Edit Warehouse",
                'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                'n' => $nTry
            ));

            $viewModel->setTemplate("inventory/warehouse/crud2");
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/inventory/warehouse/list";
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $country_list = $nmtPlugin->countryList();

        $request = $this->getRequest();

        // Is Posting .................
        // ============================

        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];

            $entity = new NmtInventoryWarehouse();
            $entity->setCompany($u->getCompany());

            $errors = $this->warehouseService->saveEntity($entity, $data, $u, TRUE);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'country_list' => $country_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }
            ;

            $redirectUrl = "/inventory/warehouse/list";
            $m = sprintf("[OK] Warehouse: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = Null;

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity = new NmtInventoryWarehouse();
        $entity->setWhCountry($u->getCompany()
            ->getCountry());
        $entity->setCompany($u->getCompany());

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'country_list' => $country_list,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("inventory/warehouse/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // Is Posing
        // =============================

        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id
            );

            /** @var \Application\Entity\NmtInventoryWarehouse $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity not found';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

            $errors = $this->warehouseService->saveEntity($entity, $data, $u);
            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "Warehouse. %s"?', $entity->getId());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit Warehouse (%s). Please try later!', $entity->getId());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Warehouse #%s updated');

            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/inventory/warehouse/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;

        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $criteria = array(
            'id' => $id
        );

        /** @var \Application\Entity\NmtInventoryWarehouse $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'n' => 0,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("inventory/warehouse/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function setDefaultAction()
    {

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $criteria = array(
            'id' => $id
        );

        /** @var \Application\Entity\NmtInventoryWarehouse $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->warehouseService->setDefaultWarehouse($entity, $u);

        $redirectUrl = "/inventory/warehouse/list";
        $m = sprintf("[OK] Warehouse: %s is set as default!", $entity->getId());
        $this->flashMessenger()->addMessage($m);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findBy(array(), array(
            'whName' => 'ASC'
        ));
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        /*
         * if (!$request->isXmlHttpRequest ()) {
         * return $this->redirect ()->toRoute ( 'access_denied' );
         * }
         */
        $this->layout("layout/user/ajax");
        $target_id = $_GET['target_id'];
        $target_name = $_GET['target_name'];

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findBy(array(), array(
            'whName' => 'ASC'
        ));
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null,
            'target_id' => $target_id,
            'target_name' => $target_name
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function transferAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $request->getHeader('Referer')->getUri();

        $item_id = (int) $this->params()->fromQuery('item_id');

        $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);

        return new ViewModel(array(
            "item" => $item,
            "errors" => null,
            'redirectUrl' => $redirectUrl
        ));
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return \Inventory\Service\WarehouseService
     */
    public function getWarehouseService()
    {
        return $this->warehouseService;
    }

    /**
     *
     * @param \Inventory\Service\WarehouseService $warehouseService
     */
    public function setWarehouseService(\Inventory\Service\WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     *
     * @return \Inventory\Application\Service\Warehouse\WarehouseService
     */
    public function getWhService()
    {
        return $this->whService;
    }

    /**
     *
     * @param WarehouseService $whService
     */
    public function setWhService(WarehouseService $whService)
    {
        $this->whService = $whService;
    }
}

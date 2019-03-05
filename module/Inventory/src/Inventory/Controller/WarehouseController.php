<?php
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseController extends AbstractActionController
{

    protected $warehouseService;

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

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

            try {
                $errors = $this->warehouseService->validateWareHouse($entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'country_list' => $country_list
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }
            ;

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            try {
                $this->warehouseService->saveWarehouse($entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // second check.

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'country_list' => $country_list
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

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
            // return $this->redirect()->toRoute('access_denied');
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
            'country_list' => $country_list
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
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $country_list = $nmtPlugin->countryList();

        $request = $this->getRequest();

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

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'country_list' => $country_list,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

            // entity found

            $oldEntity = clone ($entity);

            try {
                $errors = $this->warehouseService->validateWareHouse($entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            /**
             *
             * @todo: problem when both attribut is 0
             */
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            if (count($changeArray) == 0) {
                $nTry ++;
                $errors[] = sprintf('Nothing changed! n = %s', $nTry);
            }

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
                    'country_list' => $country_list,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $changeOn = new \DateTime();

            try {
                $this->warehouseService->saveWarehouse($entity, $u, FALSE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'country_list' => $country_list,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("inventory/warehouse/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Warehouse #%s updated. Change No.=%s.', $entity->getId(), count($changeArray));

            // Trigger Change Log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('inventory.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,
                'objectId' => $entity->getId(),
                'objectToken' => $entity->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $changeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => null
            ));

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
            'country_list' => $country_list,
            'n' => 0
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
	
	
}

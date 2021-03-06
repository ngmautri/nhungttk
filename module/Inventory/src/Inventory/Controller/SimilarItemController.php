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
class SimilarItemController extends AbstractActionController
{

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

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}

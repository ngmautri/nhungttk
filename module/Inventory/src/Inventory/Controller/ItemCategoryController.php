<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtInventoryItemCategory;
use Application\Service\ItemCategoryService;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemCategoryController extends AbstractGenericController
{

    const ROOT_NODE = '_ROOT_';

    const MGF_CATALOG_ROOT = 'MFG_CATALOG';

    protected $tree;

    protected $itemCategoryService;

    protected $itemCatService;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function initAction()
    {
        $identity = $this->identity();
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $identity
        ));

        $status = "initial...";

        // create ROOT NODE
        $e = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findBy(array(
            'nodeName' => self::ROOT_NODE
        ));
        if (count($e) == 0) {
            // create ROOT

            $input = new NmtInventoryItemCategory();
            $input->setNodeName(self::ROOT_NODE);
            $input->setPathDepth(1);
            $input->setRemarks('Node Root');
            $input->setNodeCreatedBy($u);
            $input->setNodeCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $root_id = $input->getNodeId();
            $root_node = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $root_id);
            $root_node->setPath($root_id . '/');
            $this->doctrineEM->flush();
            $status = 'Root node has been created successfully: ' . $root_id;
        } else {
            $status = 'Root node has been created already.';
        }
        return new ViewModel(array(
            'status' => $status
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $this->layout("Inventory/layout-blank");

        $root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array(
            "nodeName" => "_ROOT_"
        ));

        $this->itemCategoryService->initCategory();
        $this->itemCategoryService->updateCategory($root->getNodeId(), 0);
        $jsTree = $this->itemCategoryService->generateJSTreeNew($root->getNodeId(), false);

        $request = $this->getRequest();
        $viewModel = new ViewModel(array(
            'jsTree' => $jsTree
        ));

        $viewModel->setTemplate("inventory/item-category/list2");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateListAction()
    {
        $this->layout("Inventory/layout-blank");

        $root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array(
            "nodeName" => "_ROOT_"
        ));

        $this->itemCategoryService->initCategory();
        $this->itemCategoryService->updateCategory($root->getNodeId(), 0);
        $jsTree = $this->itemCategoryService->generateJSTreeNew($root->getNodeId(), false);

        $request = $this->getRequest();

        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/user/ajax" );
         * }
         *
         */
        // $jsTree = $this->tree;
        $viewModel = new ViewModel(array(
            'jsTree' => $jsTree
        ));

        $viewModel->setTemplate("inventory/item-category/list3");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");

        // $user = $this->userTable->getUserByEmail ( $this->identity());

        $catId = $this->params()->fromQuery('cat_id');
        $catName = $this->params()->fromQuery('cat_name');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 16;
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

        $limit = 0;
        $offset = 0;
        $paginator = null;

        if ($catId == 50) {
            $total_records = $this->getItemCatService()->getNoneCategorizedItemsTotal($limit, $offset);
        } else {
            $total_records = $this->getItemCatService()->getTotalItemsByCategory($catId, $limit, $offset);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        if ($catId == 50) {
            $records = $this->getItemCatService()->getNoneCategorizedItems($limit, $offset);
        } else {
            $records = $this->getItemCatService()->getItemsByCategory($catId, $limit, $offset);
        }

        $viewModel = new ViewModel(array(
            'list' => $records,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'cat_id' => $catId,
            'cat_name' => $catName,
            'nmtPlugin' => $nmtPlugin,
            'page' => $page
        ));

        $viewModel->setTemplate("inventory/item-category/show1");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addMemberAction()
    {
        $itemId = (int) $this->params()->fromQuery('item_id');
        $catId = (int) $this->params()->fromQuery('cat_id');

        try {

            $result = $this->getItemCatService()->addItemToCategory($itemId, $catId, $this->getUserId());
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        $viewModel = new ViewModel(array(
            'itemId' => $itemId,
            'cat_id' => $catId,
            'result' => $result
        ));

        $viewModel->setTemplate("inventory/item-category/add-member1");
        return $viewModel;
    }

    // DEPRECATED.
    // ++++++++++++++++++++++++++++++++++++++

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array(
            "nodeName" => "_ROOT_"
        ));

        $this->itemCategoryService->initCategory();
        $this->itemCategoryService->updateCategory($root->getNodeId(), 0);
        $jsTree = $this->itemCategoryService->generateJSTreeForAddingMember($root->getNodeId(), false);

        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Expires', '3800', true);
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Cache-Control', 'public', true);
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Cache-Control', 'max-age=3800');
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Pragma', '', true);

        // $jsTree = $this->tree;
        return new ViewModel(array(
            'jsTree' => $jsTree
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function changeListAction()
    {
        $this->layout("Inventory/layout-blank");

        $root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array(
            "nodeName" => "_ROOT_"
        ));

        $this->itemCategoryService->initCategory();
        $this->itemCategoryService->updateCategory($root->getNodeId(), 0);
        $jsTree = $this->itemCategoryService->generateJSTreeNew($root->getNodeId(), false);

        $request = $this->getRequest();

        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/user/ajax" );
         * }
         *
         */
        // $jsTree = $this->tree;
        $viewModel = new ViewModel(array(
            'jsTree' => $jsTree
        ));

        $viewModel->setTemplate("inventory/item-category/list3");
        return $viewModel;
    }

    /**
     *
     * @deprecated
     * @version 1
     * @copyright
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        if ($request->isPost()) {

            $node_name = $request->getPost('node_name');
            $has_member = $request->getPost('has_member');
            $status = $request->getPost('status');
            $remarks = $request->getPost('remarks');

            $errors = array();

            if ($node_name === '' or $node_name === null) {
                $errors[] = 'Please give the name of department';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findBy(array(
                'nodeName' => $node_name
            ));

            if (count($r) >= 1) {
                $errors[] = $node_name . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors,
                    'nodes' => null,
                    'parent_id' => null
                ));
            }

            // No Error
            $parent_id = $request->getPost('parent_id');
            $parent_entity = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $parent_id);

            $entity = new NmtInventoryItemCategory();
            $entity->setNodeName($node_name);
            $entity->setNodeParentId($parent_entity->getNodeId());
            $entity->setNodeCreatedOn(new \DateTime());
            $entity->setNodeCreatedby($u);

            $entity->setHasMember($has_member);
            $entity->setStatus($status);
            $entity->setRemarks($remarks);

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $new_id = $entity->getNodeId();

            $new_entity = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $new_id);
            $new_entity->setPath($parent_entity->getPath() . $new_id . '/');

            $a = explode('/', $new_entity->getPath());
            $new_entity->setPathDepth(count($a) - 1);

            $this->doctrineEM->flush();
            $redirectUrl = $request->getPost('redirectUrl');
            $this->redirect()->toUrl($redirectUrl);
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $parent_id = (int) $this->params()->fromQuery('parent_id');

        $nodes = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findAll();
        return new ViewModel(array(
            'errors' => null,
            'nodes' => $nodes,
            'parent_id' => $parent_id,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function show1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");

        // $user = $this->userTable->getUserByEmail ( $this->identity());

        $cat_id = $this->params()->fromQuery('cat_id');
        if ($cat_id > 0) {

            $query = 'SELECT e, i FROM Application\Entity\NmtInventoryItemCategoryMember e JOIN e.item i Where 1=?1';
            $query = $query . " AND e.category = " . $cat_id;
            $query = $query . " AND i.isActive = 1";
            $query = $query . ' ORDER BY i.itemName ASC';

            $records = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => 1
            ))
                ->getResult();

            // $records= $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemCategoryMember' )->findBy ( $criteria, $sort_criteria );
            // $records = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategoryMember')->findBy(array('category'=>$cat_id));
        }

        $total_records = count($records);
        $paginator = null;

        /*
         * $this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
         * $this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
         */

        return new ViewModel(array(
            'records' => $records,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'cat_id' => $cat_id
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function albumAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");
        $cat_id = $this->params()->fromQuery('cat_id');
        $album = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getAlbum($cat_id);
        return new ViewModel(array(
            'album' => $album
        ));
    }

    // +++++++++++++++++++
    // GETTET AND SETTER
    public function getItemCategoryService()
    {
        return $this->itemCategoryService;
    }

    public function setItemCategoryService(ItemCategoryService $itemCategoryService)
    {
        $this->itemCategoryService = $itemCategoryService;
        return $this;
    }

    /**
     *
     * @return \Inventory\Application\Service\Item\ItemCategoryService
     */
    public function getItemCatService()
    {
        return $this->itemCatService;
    }

    /**
     *
     * @param \Inventory\Application\Service\Item\ItemCategoryService $itemCatService
     */
    public function setItemCatService(\Inventory\Application\Service\Item\ItemCategoryService $itemCatService)
    {
        $this->itemCatService = $itemCatService;
    }
}

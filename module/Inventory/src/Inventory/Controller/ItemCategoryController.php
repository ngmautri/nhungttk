<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Application\Entity\NmtApplicationAclRoleResource;
use Application\Entity\NmtApplicationAclUserRole;
use Application\Entity\NmtInventoryItemCategory;
use Application\Model\AclRole;
use Application\Model\AclRoleTable;
use Application\Service\DepartmentService;
use Application\Service\ItemCategoryService;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use User\Model\AclRoleResource;
use User\Model\AclUserRole;
use User\Model\UserTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author nmt
 *        
 */
class ItemCategoryController extends AbstractActionController
{

    const ROOT_NODE = '_ROOT_';

    protected $SmtpTransportService;

    protected $authService;

    protected $userTable;

    protected $tree;

    protected $itemCategoryService;

    protected $itemCatService;

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

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

        $limit =0;
        $offset =0;
        $paginator = null;
        
        if ($catId == 50) {
            $total_records = $this->getItemCatService()->getNoneCategorizedItemsTotal($limit,$offset);
        } else {
            $total_records = $this->getItemCatService()->getTotalItemsByCategory($catId,$limit,$offset);
        }
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit =($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset =$paginator->minInPage - 1;
        } 

        if ($catId == 50) {
            $records = $this->getItemCatService()->getNoneCategorizedItems($limit,$offset);
        } else {
            $records = $this->getItemCatService()->getItemsByCategory($catId,$limit,$offset);
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

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addMemberAction()
    {
        $itemId = (int) $this->params()->fromQuery('item_id');
        $catId = (int) $this->params()->fromQuery('cat_id');
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));
        try {

            $result = $this->getItemCatService()->addItemToCategory($itemId, $catId, $u->getId());
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

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addMemberActionOld()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('id');
            $user_id_list = $request->getPost('users');

            if (count($user_id_list) > 0) {

                foreach ($user_id_list as $user_id) {
                    $member = new AclUserRole();
                    $member->role_id = $role_id;
                    $member->user_id = $user_id;
                    $member->updated_by = $user['id'];

                    if ($this->aclUserRoleTable->isMember($user_id, $role_id) == false) {
                        $this->aclUserRoleTable->add($member);
                    }
                }

                /*
                 * return new ViewModel ( array (
                 * 'sparepart' => null,
                 * 'categories' => $categories,
                 *
                 * ) );
                 */

                $redirectUrl = $request->getPost('redirectUrl');
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('id');
        $role = $this->aclRoleTable->getRole($id);

        $users = $this->aclUserRoleTable->getNoneMembersOfRole($id);

        return new ViewModel(array(
            'role' => $role,
            'users' => $users,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @deprecated create New Role
     */
    public function addActionOld()
    {
        $request = $this->getRequest();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);

        if ($request->isPost()) {

            // $input->status = $request->getPost ( 'status' );
            // $input->remarks = $request->getPost ( 'description' );

            $role_name = $request->getPost('role');

            $errors = array();

            if ($role_name === '' or $role_name === null) {
                $errors[] = 'Please give role name';
            }

            if ($this->aclRoleTable->isRoleExits($role_name) === true) {
                $errors[] = $role_name . ' exists';
            }

            $response = $this->getResponse();

            if (count($errors) > 0) {
                $c = array(
                    'status' => '0',
                    'messages' => $errors
                );
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($c));
                return $response;
            }

            $input = new AclRole();
            $input->role = $role_name;
            $input->parent_id = $this->aclRoleTable->getRoleIDByName($request->getPost('parent_name'));
            $input->created_by = $user["id"];

            // actually Role_name
            $role_id = $request->getPost('role_id');

            if ($this->aclRoleTable->isRoleExits($role_id) === true) {
                $this->aclRoleTable->updateByRole($input, $role_id);

                $c = array(
                    'status' => '1',
                    'messages' => array(
                        "Updated"
                    )
                );
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($c));
                return $response;
            } else {
                // role name must unique
                $new_role_id = $this->aclRoleTable->add($input);

                // get path of parent and update new role
                if ($input->parent_id !== null) :
                    $path = $this->aclRoleTable->getRole($input->parent_id)->path;
                    $path = $path . $new_role_id . '/';
                    $input->path = $path;
                else :
                    $input->path = $new_role_id . '/';
                endif;

                $a = explode('/', $input->path);
                $input->path_depth = count($a) - 1;
                $new_role_id = $this->aclRoleTable->update($input, $new_role_id);

                $c = array(
                    'status' => '1',
                    'messages' => array(
                        "Created"
                    )
                );
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($c));
                return $response;
            }
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function grantAccessAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('role_id');
            $resources = $request->getPost('resources');

            if (count($resources) > 0) {

                foreach ($resources as $r) {

                    // if not granted
                    $criteria = array(
                        'role' => $role_id,
                        'resource' => $r
                    );

                    $isGranted = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRoleResource')->findBy($criteria);

                    if (count($isGranted) == 0) {
                        $e = new NmtApplicationAclRoleResource();

                        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
                        $e->setRole($role);

                        $resources = $this->doctrineEM->find('Application\Entity\NmtApplicationAclResource', $r);
                        $e->setResource($resources);

                        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);
                        $e->setUpdatedBy($u);

                        $e->setUpdatedOn(new \DateTime());
                        $this->doctrineEM->persist($e);
                        $this->doctrineEM->flush();
                    }

                    /*
                     * if ($this->aclRoleResourceTable->isGrantedAccess ( $role_id, $r ) == false) {
                     * $access = new AclRoleResource ();
                     * $access->resource_id = $r;
                     * $access->role_id = $role_id;
                     * $this->aclRoleResourceTable->add ( $access );
                     * }
                     */
                }

                /*
                 * return new ViewModel ( array (
                 * 'sparepart' => null,
                 * 'categories' => $categories,
                 *
                 * ) );
                 */
                $this->redirect()->toUrl("/application/role/grant-access?id=" . $role_id);
            }
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 18;
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
        $role_id = $this->params()->fromQuery('id');

        $resources = $this->aclRoleTable->getNoneResourcesOfRole($role_id, 0, 0);

        $totalResults = count($resources);

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->aclRoleTable->getNoneResourcesOfRole($role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'total_resources' => $totalResults,
            'role_id' => $role_id,

            'resources' => $resources,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function grantAccessActionOld()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('role_id');
            $resources = $request->getPost('resources');

            if (count($resources) > 0) {

                foreach ($resources as $r) {
                    if ($this->aclRoleResourceTable->isGrantedAccess($role_id, $r) == false) {
                        $access = new AclRoleResource();
                        $access->resource_id = $r;
                        $access->role_id = $role_id;
                        $this->aclRoleResourceTable->add($access);
                    }
                }

                /*
                 * return new ViewModel ( array (
                 * 'sparepart' => null,
                 * 'categories' => $categories,
                 *
                 * ) );
                 */
                $this->redirect()->toUrl("/user/role/grant-access?id=" . $role_id);
            }
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 18;
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
        $role_id = $this->params()->fromQuery('id');

        $resources = $this->aclResourceTable->getNoneResourcesOfRole($role_id, 0, 0);
        $totalResults = $resources->count();

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->aclResourceTable->getNoneResourcesOfRole($role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'total_resources' => $totalResults,
            'role_id' => $role_id,

            'resources' => $resources,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function grantAccess1Action()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('role_id');
            $resources = $request->getPost('resources');

            if (count($resources) > 0) {

                foreach ($resources as $r) {
                    if ($this->aclRoleResourceTable->isGrantedAccess($role_id, $r) == false) {
                        $access = new AclRoleResource();
                        $access->resource_id = $r;
                        $access->role_id = $role_id;
                        $this->aclRoleResourceTable->add($access);
                    }
                }

                /*
                 * return new ViewModel ( array (
                 * 'sparepart' => null,
                 * 'categories' => $categories,
                 *
                 * ) );
                 */
                $this->redirect()->toUrl("/user/role/grant-access?id=" . $role_id);
            }
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 18;
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
        $role = $this->params()->fromQuery('role');
        $role_id = $this->aclRoleTable->getRoleIDByName($role);

        $resources = $this->aclResourceTable->getNoneResourcesOfRole($role_id, 0, 0);
        $totalResults = $resources->count();

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->aclResourceTable->getNoneResourcesOfRole($role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'total_resources' => $totalResults,
            'role_id' => $role_id,

            'resources' => $resources,
            'paginator' => $paginator
        ));
    }

    public function getAclRoleTable()
    {
        return $this->aclRoleTable;
    }

    public function setAclRoleTable(AclRoleTable $aclRoleTable)
    {
        $this->aclRoleTable = $aclRoleTable;
        return $this;
    }

    public function getSmtpTransportService()
    {
        return $this->SmtpTransportService;
    }

    public function setSmtpTransportService($SmtpTransportService)
    {
        $this->SmtpTransportService = $SmtpTransportService;
        return $this;
    }

    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
        return $this;
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

    public function getDepartmentService()
    {
        return $this->departmentService;
    }

    public function setDepartmentService(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
        return $this;
    }

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

<?php
namespace Application\Controller;

use Application\Notification;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\InsertDepartmentCmdHandler;
use Application\Application\Command\Doctrine\Company\MoveDepartmentCmdHandler;
use Application\Application\Command\Doctrine\Company\RemoveDepartmentCmdHandler;
use Application\Application\Command\Doctrine\Company\RenameDepartmentCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Application\Service\Department\Tree\Output\DepartmentJsTreeFormatter;
use Application\Application\Service\Department\Tree\Output\DepartmentWithOneLevelForOptionFormatter;
use Application\Application\Service\Department\Tree\Output\DepartmentWithRootForOptionFormatter;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Tree\Output\ForSelectListFormatter;
use Application\Domain\Util\Tree\Output\OrnigrammFormatter;
use Application\Entity\NmtApplicationAclUserRole;
use Application\Entity\NmtApplicationDepartment;
use Zend\Escaper\Escaper;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentController extends AbstractGenericController
{

    const ROOT_NODE = '_COMPANY_';

    protected $baseUrl;

    protected $defaultLayout;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    public function moveAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/move";
        $form_title = "Edit Form";
        $action = FormActions::MOVE;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $builder = new DepartmentTree();
        $builder->setDoctrineEM($this->getDoctrineEM());

        $builder->initTree();
        $root = $builder->createTree(1, 0);

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $departmentName = $this->params()->fromQuery('n');
            $departmentNode = $root->getNodeByName($departmentName);

            if ($departmentNode == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'departmentName' => $departmentName,
                'departmentNode' => $departmentNode,
                'parentName' => null,

                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter())
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            // POSTING
            $data = $prg;

            $departmentName = $data['departmentName'];

            $departmentNode = $root->getNodeByName($departmentName);

            if ($departmentNode == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);

            $cmdHandler = new MoveDepartmentCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            // left blank
        }

        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
                'departmentName' => $departmentName,
                'departmentNode' => $departmentNode,
                'parentName' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter())
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = $this->getBaseUrl() . "/list2";
        return $this->redirect()->toUrl($redirectUrl);
    }

    public function renameAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();

        try {

            $nodeName = $escaper->escapeHtml($_POST['node_name']);
            $newName = $escaper->escapeHtml($_POST['new_name']);

            $this->logInfo($nodeName);

            $builder = new DepartmentTree();
            $builder->setDoctrineEM($this->getDoctrineEM());
            $builder->initTree();
            $root = $builder->createTree(1, 0);

            $node = $root->getNodeByName($nodeName);

            $this->logInfo($node->getNodeName());

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);

            $cmdHandler = new RenameDepartmentCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $data['node'] = $node;
            $data['newName'] = $newName;
            $data['builder'] = $builder;

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getTraceAsString());
            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(\json_encode("[Failed] Node name not changed.Please see log!"));
            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Node name changed!"));
        return $response;
    }

    public function removeAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();

        try {

            $nodeName = $escaper->escapeHtml($_POST['node_name']);

            $builder = new DepartmentTree();
            $builder->setDoctrineEM($this->getDoctrineEM());
            $builder->initTree();
            $root = $builder->createTree(1, 0);

            $node = $root->getNodeByName($nodeName);

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);

            $cmdHandler = new RemoveDepartmentCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $data['node'] = $node;
            $data['builder'] = $builder;

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getTraceAsString());
            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(\json_encode("[Failed] Node name not removed.Please see log!"));
            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Node removed!"));
        return $response;
    }

    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $builder = new DepartmentTree();
        $builder->setDoctrineEM($this->getDoctrineEM());

        $builder->initTree();
        $root = $builder->createTree(1, 0);

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentName = $this->params()->fromQuery('p');

            $viewModel = new ViewModel(array(
                'errors' => null,
                'departmentName' => null,
                'departmentNode' => null,
                'parentName' => $parentName,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter())
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new InsertDepartmentCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'departmentName' => null,
                'departmentNode' => null,

                'parentName' => null,

                'redirectUrl' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter())
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = $this->getBaseUrl() . "/list2";

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function updateAction()
    {}

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function initAction()
    {
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $status = "initial...";

        // create ROOT NODE
        $e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->findBy(array(
            'nodeName' => self::ROOT_NODE
        ));
        if (count($e) == 0) {
            // create super admin

            $input = new NmtApplicationDepartment();
            $input->setNodeName(self::ROOT_NODE);
            $input->setPathDepth(1);
            $input->setRemarks('Node Root');
            $input->setNodeCreatedBy($u);
            $input->setNodeCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $root_id = $input->getNodeId();
            $root_node = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $root_id);
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
     * @deprecated
     * @version 3.0
     * @author Ngmautri
     *
     *         Create new Department
     */
    public function addAction()
    {
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $parent_id = (int) $this->params()->fromQuery('parent_id');

        $request = $this->getRequest();

        if ($request->isPost()) {

            // $input->status = $request->getPost ( 'status' );
            // $input->remarks = $request->getPost ( 'description' );

            $node_name = $request->getPost('node_name');

            $errors = array();

            if ($node_name === '' or $node_name === null) {
                $errors[] = 'Please give the name of department';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->findBy(array(
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
            $parent_entity = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $parent_id);
            // var_dump($parent_entity->getPath());

            $entity = new NmtApplicationDepartment();
            $entity->setNodeName($node_name);
            $entity->setNodeParentId($parent_entity->getNodeId());
            $entity->setCreatedOn(new \DateTime());
            $entity->setCreatedBy($u);
            $entity->setStatus("activated");
            $entity->setRemarks('done');

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $new_id = $entity->getNodeId();

            $new_entity = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $new_id);
            $new_entity->setPath($parent_entity->getPath() . $new_id . '/');

            $a = explode('/', $new_entity->getPath());
            $new_entity->setPathDepth(count($a) - 1);

            $this->doctrineEM->flush();
        }

        $node = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->findAll();
        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/inventory/ajax" );
         * }
         */

        $builder = new DepartmentTree();
        $builder->setDoctrineEM($this->getDoctrineEM());

        $builder->initTree();
        $root = $builder->createTree(1, 0);

        return new ViewModel(array(
            'errors' => null,
            'nodes' => $node,
            'parent_id' => $parent_id,
            'jsTree' => $root->display(new DepartmentJsTreeFormatter()),
            'simpleTree' => $root->display(new ForSelectListFormatter()),
            'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter())
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $this->departmentService->initCategory();
        $this->departmentService->updateCategory(1, 0);
        $jsTree = $this->departmentService->generateJSTree(1);

        // $jsTree = $this->tree;
        return new ViewModel(array(
            'jsTree' => $jsTree
        ));
    }

    public function list2Action()
    {
        $builder = new DepartmentTree();
        $builder->setDoctrineEM($this->getDoctrineEM());

        $builder->initTree();
        $root = $builder->createTree(1, 0);

        return new ViewModel(array(
            'jsTree' => $root->display(new DepartmentJsTreeFormatter()),
            'simpleTree' => $root->display(new ForSelectListFormatter()),
            'departmentForOption' => $root->display(new DepartmentWithRootForOptionFormatter()),
            'departmentWithOnelevelForOption' => $root->display(new DepartmentWithOneLevelForOptionFormatter()),
            'departmentForOrnigramm' => $root->display(new OrnigrammFormatter())
        ));
    }

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

        $this->departmentService->initCategory();
        $this->departmentService->updateCategory(1, 0);
        $jsTree = $this->departmentService->generateJSTree(1);

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
    public function addMemberAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('id');
            $user_id_list = $request->getPost('users');

            if (count($user_id_list) > 0) {
                foreach ($user_id_list as $member_id) {

                    /*
                     * $member = new AclUserRole ();
                     * $member->role_id = $role_id;
                     * $member->user_id = $user_id;
                     * $member->updated_by = $user ['id'];
                     */
                    // echo $member_id;

                    $criteria = array(
                        'user' => $member_id,
                        'role' => $role_id
                    );

                    $isMember = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclUserRole')->findBy($criteria);
                    // var_dump($isMember);
                    if (count($isMember) == 0) {
                        $member = new NmtApplicationAclUserRole();
                        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
                        $member->setRole($role);
                        $m = $this->doctrineEM->find('Application\Entity\MlaUsers', $member_id);
                        $member->setUser($m);
                        $member->setUpdatedBy($u);
                        $member->setUpdatedOn(new \DateTime());
                        $this->doctrineEM->persist($member);
                        $this->doctrineEM->flush();
                    }
                }

                $redirectUrl = $request->getPost('redirectUrl');
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('id');
        // $role = $this->aclRoleTable->getRole ( $id );
        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $id);

        /**
         *
         * @todo
         */
        $users = $this->aclRoleTable->getNoneMembersOfRole($id);

        return new ViewModel(array(
            'role' => $role,
            'users' => $users,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return "/application/department";
    }

    /**
     *
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLayout()
    {
        return "Application/layout-fluid";
    }

    /**
     *
     * @param mixed $defaultLayout
     */
    public function setDefaultLayout($defaultLayout)
    {
        $this->defaultLayout = $defaultLayout;
    }
}

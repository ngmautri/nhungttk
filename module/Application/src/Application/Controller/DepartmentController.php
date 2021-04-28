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
use Application\Application\Service\Department\Tree\Output\PureDepartmentWithRootForOptionFormatter;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Tree\Output\ForSelectListFormatter;
use Application\Domain\Util\Tree\Output\OrnigrammFormatter;
use Application\Form\Deparment\DepartmentForm;
use Zend\Escaper\Escaper;
use Zend\Http\Response;
use Zend\Stdlib\Hydrator\Reflection;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentController extends AbstractGenericController
{

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

            // $this->logInfo($node->getNodeName());

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

        $form = new DepartmentForm("dept_create_form");
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/application/department/list2');
        $departmentOptions = $root->display(new PureDepartmentWithRootForOptionFormatter());
        $form->setDepartmentOptions($departmentOptions);
        $form->setFormAction($action);
        $form->refresh();

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentName = $this->params()->fromQuery('p');
            $snapshot = new DepartmentSnapshot();
            $snapshot->setParentName($parentName);
            $form->bind($snapshot);

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
                'form' => $form
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

        $form->bind($cmd->getOutput());

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
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
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

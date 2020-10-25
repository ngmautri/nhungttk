<?php
namespace Application\Controller\Contracts;

use Application\Application\Service\ValueObject\ValueObjectService;
use Application\Domain\Contracts\FormActions;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use MLA\Paginator;
use Zend\View\Model\ViewModel;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class CRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $listTemplate;

    protected $ajaxLayout;

    protected $valueObjectService;

    protected $crudRepository;

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

    abstract protected function setListTemplate();

    public function listAction()
    {
        $sortBy = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $page = $this->params()->fromQuery('page');
        $resultsPerPage = $this->params()->fromQuery('perPage');

        if ($resultsPerPage == null) {
            $resultsPerPage = 15;
        }

        if ($page == null) {
            $page = 1;
        }

        $limit = null;
        $offset = null;
        $paginator = null;

        $total_records = $this->getValueObjectService()->getTotal();

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $filter = new DefaultListSqlFilter();
        $filter->setSortBy($sortBy);
        $filter->setSort($sort);
        $filter->setLimit($limit);
        $filter->setOffset($offset);
        $list = $this->getValueObjectService()->getValueCollecion($filter);

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'filter' => $filter
        ));
        $viewModel->setTemplate($this->getListTemplate());
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create PO";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'key' => null,
                'dto' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $prg['createdBy'] = $this->getUserId();
            $prg['company'] = $this->getCompanyId();
            $this->getValueObjectService()->addFrom($prg);
        } catch (\Exception $e) {

            $this->logException($e);
            $viewModel = new ViewModel(array(
                'errors' => [
                    $e->getMessage()
                ],
                'redirectUrl' => null,
                'entity_id' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'key' => null,
                'dto' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf($this->getBaseUrl() . "/list");
        $this->logInfo(\sprintf("%s", ""));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $this->layout($this->getDefaultLayout());

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = $this->getBaseUrl() . "/update";

        $form_title = "Edit PO";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $key = $this->params()->fromQuery('key');
            $valueObject = $this->getValueObjectService()->getByKey($key);

            if ($valueObject == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'key' => $key,
                'dto' => $valueObject->makeSnapshot()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {

            // POSTING

            $data = $prg;
            $key = $data['key'];
            $valueObject = $this->getValueObjectService()->getByKey($key);

            if ($valueObject == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $prg['createdBy'] = $this->getUserId();
            $prg['company'] = $this->getCompanyId();

            $this->getValueObjectService()->update($data['key'], $data);
        } catch (\Exception $e) {
            $viewModel = new ViewModel(array(
                'errors' => [
                    $e->getMessage()
                ],
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'key' => $key,
                'dto' => $valueObject->makeSnapshot()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = $this->getBaseUrl() . '/list';

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $this->layout($this->getDefaultLayout());
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $key = $this->params()->fromQuery('key');
        $valueObject = $this->getValueObjectService()->getByKey($key);

        if ($valueObject == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $form_action = $this->getBaseUrl() . "/show";
        $viewTemplete = $this->getBaseUrl() . "/crud";

        $viewModel = new ViewModel(array(
            'action' => FormActions::SHOW,
            'form_action' => $form_action,
            'form_title' => $nmtPlugin->translate("Show"),
            'redirectUrl' => null,
            'errors' => null,
            'version' => null,
            'key' => $key,
            'nmtPlugin' => $nmtPlugin,
            'valueObject' => $valueObject
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    public function deleteAction()
    {}

    /**
     *
     * @return \Application\Application\Service\ValueObject\ValueObjectService
     */
    public function getValueObjectService()
    {
        return $this->valueObjectService;
    }

    /**
     *
     * @return \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface
     */
    public function getCrudRepository()
    {
        return $this->crudRepository;
    }

    /**
     *
     * @param ValueObjectService $valueObjectService
     */
    public function setValueObjectService(ValueObjectService $valueObjectService)
    {
        $this->valueObjectService = $valueObjectService;
    }

    /**
     *
     * @param CrudRepositoryInterface $crudRepository
     */
    public function setCrudRepository(CrudRepositoryInterface $crudRepository)
    {
        $this->crudRepository = $crudRepository;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLayout()
    {
        return $this->defaultLayout;
    }

    /**
     *
     * @return mixed
     */
    public function getAjaxLayout()
    {
        return $this->ajaxLayout;
    }

    /**
     *
     * @return mixed
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }
}

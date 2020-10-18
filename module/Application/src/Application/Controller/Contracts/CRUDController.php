<?php
namespace Application\Controller\Contracts;

use Application\Application\Service\ValueObject\ValueObjectService;
use Application\Domain\Contracts\FormActions;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class CRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $ajaxLayout;

    protected $valueObjectService;

    protected $crudRepository;

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

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
                'key' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {
            $data = $prg;
            $this->getValueObjectService()->add($data);
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
                'valueObject' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf($this->getBaseUrl() . "/list");
        $this->getLogger()->info(\sprintf("%s", ""));

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

            $key = (int) $this->params()->fromQuery('key');
            $valueObject = $this->getValueObjectService()->add($key);

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
                'key' => null,
                'valueObject' => $valueObject
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }
        try {

            // POSTING
            $data = $prg;
            $this->getValueObjectService()->update($key, $data);
        } catch (\Exception $e) {}

        $redirectUrl = sprintf("%s", "");

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

    public function getListAction()
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
}

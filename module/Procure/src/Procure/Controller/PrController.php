<?php
namespace Procure\Controller;

use Application\Domain\Contracts\FormActions;
use Procure\Controller\Contracts\ProcureCRUDController;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends ProcureCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
        $this->setViewTemplate();
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/procure/pr';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/pr/review-v2";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/pr/list';
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function view1Action()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());

        $filter = new PrRowReportSqlFilter();
        $page = 1;
        $resultPerPage = 10;

        $render = $this->getProcureService()->getRowCollectionRender($rootEntity, $filter, $page, $resultPerPage);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowCollectionRender' => $render,
            'headerDTO' => $rootEntity->makeSnapshot(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }
}

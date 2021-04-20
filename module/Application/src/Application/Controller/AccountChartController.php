<?php
namespace Application\Controller;

use Application\Controller\Contracts\EntityCRUDController;
use Application\Domain\Company\AccountChart\GenericChart;
use Application\Domain\Contracts\FormActions;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountChartController extends EntityCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Controller\Contracts\CRUDController::setBaseUrl()
     */
    protected function setBaseUrl()
    {
        $this->baseUrl = '/application/account-chart';
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = "layout/user/ajax";
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Application/layout-fluid";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/list';
    }

    protected function setViewTemplate()
    {}

    public function listAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();
        $request = $this->getRequest();

        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getById($this->getCompanyId());

        /**
         *
         * @var GenericChart $chart
         */
        $chart = $results->getLazyAccountChartCollection();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'chart' => $chart,
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }
}

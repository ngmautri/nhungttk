<?php
namespace Procure\Controller;

use Application\Domain\Contracts\FormActions;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Controller\Contracts\ProcureCRUDController;
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
        $this->viewTemplate = "procure/pr/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/pr/list';
    }

    public function view1Action()
    {
        $this->layout("layout/user/ajax");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('not_found');
        }

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/pr/view1";
        $form_title = "View Purchase Request:";
        $action = FormActions::SHOW;
        $viewTemplete = "procure/pr/view-v1";

        $id = (int) $this->params()->fromQuery('entity_id');
        $rootEntity = $this->getPurchaseRequestService()->getDocDetailsByIdFromDB($id, SaveAsSupportedType::OUTPUT_IN_ARRAY);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }
        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'entity_id' => null,
            'entity_token' => null
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }
}

<?php
namespace Application\Controller;

use Application\Controller\Contracts\CRUDController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomGroupController extends CRUDController
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
        $this->baseUrl = '/application/uom-group';
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
}

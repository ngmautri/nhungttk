<?php
namespace Inventory\Controller;

use Inventory\Controller\Contracts\TrxCRUDController;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 * Transfer Warehouse
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TransferWhController extends TrxCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
        $this->setViewTemplate();
        $this->setTransactionTypes();
    }

    protected function doRedirecting($movementType, $id, $token)
    {
        // do nothing.
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/inventory/transfer-wh';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Inventory/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "inventory/transfer-wh/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/transfer-wh/list';
    }

    protected function setTransactionTypes()
    {
        $this->transactionType = TrxType::getGoodIssueTrx();
    }
}

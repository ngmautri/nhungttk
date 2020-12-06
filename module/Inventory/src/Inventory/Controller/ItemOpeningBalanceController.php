<?php
namespace Inventory\Controller;

use Inventory\Controller\Contracts\TrxCRUDController;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 * Opening Balance
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemOpeningBalanceController extends TrxCRUDController
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

    protected function redirectForView($movementType, $id, $token)
    {}

    protected function redirectForCreate($data)
    {}

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/inventory/item-opening-balance';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Inventory/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "inventory/item-opening-balance/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/inventory/item-opening-balance/list';
    }

    protected function setTransactionTypes()
    {
        $this->transactionTypes = TrxType::getGoodReceiptTrx();
    }
}

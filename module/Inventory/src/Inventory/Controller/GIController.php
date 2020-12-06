<?php
namespace Inventory\Controller;

use Inventory\Controller\Contracts\TrxCRUDController;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 * Goods Issue
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIController extends TrxCRUDController
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

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/inventory/gi';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Inventory/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "inventory/gi/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/gi/list';
    }

    protected function setTransactionTypes()
    {
        $this->transactionTypes = TrxType::getGoodIssueTrx();
    }

    protected function doRedirecting($movementType, $id, $token)
    {
        $redirectUrl = null;
        switch ($movementType) {
            case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                $f = '/inventory/transfer-wh/view?entity_id=%s&entity_token=%s';
                $redirectUrl = sprintf($f, $id, $token);
                break;
        }

        if ($redirectUrl != null) {
            return $this->redirect()->toUrl($redirectUrl);
        }
    }
}

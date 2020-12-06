<?php
namespace Inventory\Controller;

use Inventory\Controller\Contracts\TrxCRUDController;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 * Goods Receipt
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRController extends TrxCRUDController
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
    {
        $redirectUrl = null;
        switch ($movementType) {
            case TrxType::GR_FROM_OPENNING_BALANCE:
                $f = '/inventory/item-opening-balance/view?entity_id=%s&entity_token=%s';
                $redirectUrl = sprintf($f, $id, $token);
                break;
        }

        if ($redirectUrl != null) {
            return $this->redirect()->toUrl($redirectUrl);
        }
    }

    protected function redirectForCreate($data)
    {
        $redirectUrl = null;

        $movementType = $data['movementType'];
        switch ($movementType) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $f = '/inventory/item-opening-balance/create?sourceWH=%s&movementDate=%s';
                $redirectUrl = sprintf($f, $data['warehouse'], $data['movementDate']);
        }

        if ($redirectUrl != null) {
            return $this->redirect()->toUrl($redirectUrl);
        }
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/inventory/gr';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Inventory/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "inventory/gr/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/gr/list';
    }

    protected function setTransactionTypes()
    {
        $this->transactionTypes = TrxType::getGoodReceiptTrx();
    }
}

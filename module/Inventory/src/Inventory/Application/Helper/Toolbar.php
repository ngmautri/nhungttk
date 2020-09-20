<?php
namespace Inventory\Application\Helper;

use Inventory\Domain\Transaction\TrxSnapshot;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Toolbar
{

    public static function showToolbarGI(TrxSnapshot $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/inventory/gi/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $reverse_url = sprintf("/inventory/gi/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $goods_return_url = sprintf("/inventory/gi/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodReturnBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_return_url, $view->translate("Goods Return"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }

                break;
        }

        return $toolbar;
    }

    public static function showToolbarGR(TrxSnapshot $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/inventory/gr/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $f = '<a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;"><i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>';
        $postBtn = sprintf($f, $view->translate("Post"));

        $reverse_url = sprintf("/inventory/gr/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $f = '/inventory/gi/create-return?target_id=%s&target_token=%s';
        $goods_return_url = sprintf($f, $headerDTO->getId(), $headerDTO->getToken());
        $f = '<a title="Return PO" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>';
        $goodReturnBtn = sprintf($f, $goods_return_url, $view->translate("Goods Return"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $goodReturnBtn;
                }

                break;

            case ProcureDocStatus::POSTED:

                if ($headerDTO->getMovementType() == TrxType::GR_FROM_PURCHASING) {
                    $toolbar = $goodReturnBtn;
                }

                break;
        }

        return $toolbar;
    }

    public static function showToolbarOpeningBalance(TrxSnapshot $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/inventory/item-opening-balance/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $reverse_url = sprintf("/inventory/item-opening-balance/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $goods_return_url = sprintf("/inventory/item-opening-balance/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodReturnBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_return_url, $view->translate("Goods Return"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }

                break;
        }

        return $toolbar;
    }

    public static function showToolbarTransferWH(TrxSnapshot $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/inventory/transfer-wh/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $reverse_url = sprintf("/inventory/transfer-wh/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $goods_return_url = sprintf("/inventory/transfer-wh/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodReturnBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_return_url, $view->translate("Goods Return"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }

                break;
        }

        return $toolbar;
    }
}

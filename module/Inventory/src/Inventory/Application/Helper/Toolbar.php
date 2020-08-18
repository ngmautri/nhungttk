<?php
namespace Inventory\Application\Helper;

use Inventory\Application\DTO\Transaction\TrxDTO;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Toolbar
{

    public static function showToolbarGI(TrxDTO $headerDTO, $action, $view)
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

            case ProcureDocStatus::DOC_STATUS_DRAFT:

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

    public static function showToolbarGR(TrxDTO $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/inventory/gr/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $reverse_url = sprintf("/inventory/gr/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $goods_return_url = sprintf("/inventory/gr/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodReturnBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_return_url, $view->translate("Goods Return"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DOC_STATUS_DRAFT:

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

    public static function showToolbarOpeningBalance(TrxDTO $headerDTO, $action, $view)
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

            case ProcureDocStatus::DOC_STATUS_DRAFT:

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

<?php
namespace Procure\Application\Helper;

use Procure\Application\DTO\Ap\ApDTO;
use Procure\Domain\Shared\Constants;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Toolbar
{

    public static function showToolbarAP(ApDTO $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/procure/ap/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $reverse_url = sprintf("/procure/ap/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reverseBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-undo" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $reverse_url, $view->translate("Reverse"));

        $goods_return_url = sprintf("/procure/ap/reverse?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodReturnBtn = sprintf('<a title="Reverse AP Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_return_url, $view->translate("Goods Return"));

        $pay_url = sprintf("/payment/outgoing/pay-ap?target_id=%s&token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $payBtn = sprintf('<a title="Pay Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $pay_url, $view->translate("Pay"));

        switch ($headerDTO->getDocStatus()) {

            case Constants::DOC_STATUS_DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }
                break;

            case Constants::DOC_STATUS_POSTED:
                $toolbar = \sprintf("%s %s %s", $payBtn, $goodReturnBtn, $reverseBtn);
                if ($action == Constants::FORM_ACTION_REVERSE) {
                    $toolbar = "";
                }
                break;
        }

        return $toolbar;
    }
}

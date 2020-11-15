<?php
namespace Procure\Application\Helper;

use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Application\DTO\Pr\PrDTO;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;

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

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }

                break;

            case ProcureDocStatus::POSTED:
                $toolbar = \sprintf("%s %s %s", $payBtn, $goodReturnBtn, $reverseBtn);
                if ($action == Constants::FORM_ACTION_REVERSE) {
                    $toolbar = "";
                }
                break;
        }

        return $toolbar;
    }

    public static function showToolbarPO(PoDetailsDTO $headerDTO, $action, $view)
    {
        $toolbar = "";

        $clone_url = sprintf("/procure/po/clone?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $cloneBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $clone_url, $view->translate("Clone"));

        $review_url = sprintf("/procure/po/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $review_amendment_url = sprintf("/procure/po/review-amendment?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewAmendmentBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_amendment_url, $view->translate("Review & Post"));

        $enableAmendmentBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmAmendment();" href="javascript:;" title="%s">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', "PO Amendment", $view->translate("Amendment"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        $postAmendmentBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPostAmendment();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post Amendment"));

        $pay_url = sprintf("/payment/outgoing/pay-po?target_id=%s&token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $payBtn = sprintf('<a title="Pay Invoice" class="btn btn-default btn-sm" href="%s"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $pay_url, $view->translate("Pay"));

        $goods_receipt_url = sprintf("/procure/gr/create-from-po?source_id=%s&source_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $goodsReceiptBtn = sprintf('<a title="Goods Receipt from PO" class="btn btn-default btn-sm" href="%s"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $goods_receipt_url, $view->translate("Goods Receipt"));

        $invoice_url = sprintf("/procure/ap/create-from-po?source_id=%s&source_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $invoiceBtn = sprintf('<a title="Invoice from PO" class="btn btn-default btn-sm" href="%s"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $invoice_url, $view->translate("A/P Invoice"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }
                break;

            case ProcureDocStatus::POSTED:
                $toolbar = \sprintf("%s %s %s %s %s", $cloneBtn, $enableAmendmentBtn, $goodsReceiptBtn, $invoiceBtn, $payBtn, $cloneBtn);
                if ($action == Constants::FORM_ACTION_REVERSE) {
                    $toolbar = "";
                }
                break;
            case ProcureDocStatus::AMENDING:
                $toolbar = \sprintf("%s", $postAmendmentBtn);

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = \sprintf("%s %s", $cloneBtn, $reviewAmendmentBtn);
                }
                break;

                break;
        }

        return $toolbar;
    }

    public static function showToolbarQR(QrDTO $headerDTO, $action, $view)
    {
        $po_url = sprintf("/procure/po/create-from-qr?source_id=%s&source_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $poBtn = sprintf('<a title="PO from Quotation" class="btn btn-default btn-sm" href="%s"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $po_url, $view->translate("Create PO"));
        $review_url = sprintf("/procure/qr/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));
        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:
                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }
                break;

            case ProcureDocStatus::POSTED:
                $toolbar = \sprintf("%s", $poBtn);
                if ($action == Constants::FORM_ACTION_REVERSE) {
                    $toolbar = "";
                }
                break;
        }

        return $toolbar;
    }

    public static function showToolbarPR(PrDTO $headerDTO, $action, $view)
    {
        $toolbar = "";

        $review_url = sprintf("/procure/pr/review?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
        $reviewBtn = \sprintf('<a class="btn btn-default btn-sm" href="%s"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;%s</a>', $review_url, $view->translate("Review & Post"));

        $postBtn = sprintf(' <a class="btn btn-primary btn-sm" style="color: white" onclick="confirmPost();" href="javascript:;">
<i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp;%s</a>', $view->translate("Post"));

        switch ($headerDTO->getDocStatus()) {

            case ProcureDocStatus::DRAFT:

                if ($action == Constants::FORM_ACTION_SHOW) {
                    $toolbar = $reviewBtn;
                }

                if ($action == Constants::FORM_ACTION_REVIEW) {
                    $toolbar = $postBtn;
                }
                break;

            case ProcureDocStatus::POSTED:
                break;
            case ProcureDocStatus::AMENDING:
                break;
        }

        return $toolbar;
    }
}

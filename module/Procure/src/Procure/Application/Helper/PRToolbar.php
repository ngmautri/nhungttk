<?php
namespace Procure\Application\Helper;

use Application\Application\Helper\Form\FormHelper;
use Application\Domain\Util\MyTranslator;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\Shared\ProcureDocStatus;
use Zend\Mvc\I18n\Translator as MvcTranslator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRToolbar
{

    /**
     *
     * @param PRDoc $rootEntity
     * @param unknown $renderType
     * @param MvcTranslator $mvcTranslator
     * @param unknown $locale
     * @param unknown $page
     * @param unknown $resultPerPage
     * @param unknown $resultDiv
     * @return string
     */
    public static function forHTMLRowCollection(PRDoc $rootEntity, $renderType, MvcTranslator $mvcTranslator, $locale, $page, $resultPerPage, $resultDiv)
    {
        $translator = new MyTranslator($mvcTranslator, $locale);

        $f = '/procure/pr/add-row?target_id=%s&target_token=%s';
        $add_row_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        $f = '/procure/pr/upload-rows?target_id=%s&target_token=%s';
        $upload_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        $format = "/procure/pr/row-content?entity_id=%s&entity_token=%s&renderType=%s&page=%s&resultPerPage=%s";

        $param_query_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::PARAM_QUERY, $page, $resultPerPage);

        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, $resultDiv);

        $toolbar = '';
        if ($rootEntity->getDocStatus() == ProcureDocStatus::DRAFT) {
            $l = $translator->translate("Add New Row");
            $toolbar = $toolbar . FormHelper::createButton($l, $translator->translate("add new row for purchase request"), $add_row_url, 'fa fa-plus');

            $l = $translator->translate("Upload");
            $toolbar = $toolbar . FormHelper::createButton($l, $translator->translate("upload data for purchase request"), $upload_url, 'fa fa-upload');
        }
        $toolbar = $toolbar . FormHelper::createButtonForJS('<i class="fa fa-th" aria-hidden="true"></i>', $html_onclick, $translator->translate('Gird View'));

        return $toolbar;
    }

    public static function forParamQueryRowCollection(PRDoc $rootEntity, $renderType, MvcTranslator $mvcTranslator, $locale, $page, $resultPerPage, $resultDiv)
    {
        $translator = new MyTranslator($mvcTranslator, $locale);

        $f = '/procure/pr/add-row?target_id=%s&target_token=%s';
        $add_row_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        $format = "/procure/pr/row-content?entity_id=%s&entity_token=%s&renderType=%s&page=%s&resultPerPage=%s";

        $param_query_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::PARAM_QUERY, $page, $resultPerPage);

        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, $resultDiv);

        $toolbar = '';
        if ($rootEntity->getDocStatus() == ProcureDocStatus::DRAFT) {
            $l = $translator->translate("Add New Row");
            $toolbar = $toolbar . FormHelper::createButton($l, $translator->translate("add new row for purchase request"), $add_row_url, 'fa fa-plus');
        }
        $toolbar = $toolbar . FormHelper::createButtonForJS('<i class="fa fa-th" aria-hidden="true"></i>', $html_onclick, $translator->translate('Gird View'));

        return $toolbar;
    }

    public static function forEmptyRowCollection(PRDoc $rootEntity, $renderType, MvcTranslator $mvcTranslator, $locale, $page, $resultPerPage, $resultDiv)
    {
        $translator = new MyTranslator($mvcTranslator, $locale);

        $f = '/procure/pr/add-row?target_id=%s&target_token=%s';
        $add_row_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        $f = '/procure/pr/upload-rows?target_id=%s&target_token=%s';
        $upload_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        $format = "/procure/pr/row-content?entity_id=%s&entity_token=%s&renderType=%s&page=%s&resultPerPage=%s";

        $param_query_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::PARAM_QUERY, $page, $resultPerPage);

        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, $resultDiv);

        $toolbar = '';
        if ($rootEntity->getDocStatus() == ProcureDocStatus::DRAFT) {
            $l = $translator->translate("Add New Row");
            $toolbar = $toolbar . FormHelper::createButton($l, $translator->translate("add new row for purchase request"), $add_row_url, 'fa fa-plus');

            $l = $translator->translate("Upload");
            $toolbar = $toolbar . FormHelper::createButton($l, $translator->translate("upload data for purchase request"), $upload_url, 'fa fa-upload');
        }

        return $toolbar;
    }
}

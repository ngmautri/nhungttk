<?php
namespace Procure\Application\Reporting\PO\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ListHelper
{

    public static function createTableBody($name, $title, $url, $icon)
    {
        foreach ($list as $rootSnapshot) {
            
            $n ++;
            
            if ($paginator !== null) {
                $count = $paginator->getMinInPage() - 1 + $n;
            } else {
                $count = $n;
            }
            
            $url = sprintf($this->baseUrl . "/procure/po/view?entity_id=%s&entity_token=%s", $rootSnapshot->getId(), $rootSnapshot->getToken());
            $title = "Show PO";
            $icon = null;
            $viewBtn = FormHelper::createButton("View", $title, $url, $icon);
            
            $url = sprintf($this->baseUrl . "/procure/po/update?entity_id=%s&entity_token=%s", $rootSnapshot->getId(), $rootSnapshot->getToken());
            $title = "Edit PO";
            $icon = 'glyphicon glyphicon-pencil';
            $editBtn = FormHelper::createButton($this->translate("Edit"), $title, $url, $icon);
            
            if ($rootSnapshot->getTotalRows() > 0) {
                $completion = $rootSnapshot->getCompletedGRRows() / $rootSnapshot->getTotalRows();
            } else {
                $completion = 0;
            }
            $progress_div = FormHelper::createProgressDiv($completion, null);
            
            if ($rootSnapshot->getTotalRows() > 0) {
                $completion1 = $rootSnapshot->getCompletedAPRows() / $rootSnapshot->getTotalRows();
            } else {
                $completion1 = 0;
            }
            $progress_div1 = FormHelper::createProgressDiv($completion1, null);
            
            $table_body = $table_body . "<tr>";
            $table_body = $table_body . sprintf("<td>%s</td>", $count);
            $table_body = $table_body . sprintf("<td>%s</td>", $progress_div);
            $table_body = $table_body . sprintf("<td>%s</td>", $progress_div1);
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getDocStatus());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getSysNumber());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getVendorName());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getDocNumber());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getDocDate());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getTotalRows());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getGrossAmount());
            $table_body = $table_body . sprintf("<td>%s</td>", $rootSnapshot->getCurrencyIso3());
            $table_body = $table_body . sprintf("<td>%s</td>", $viewBtn . ' ' . $editBtn);
            
            $table_body = $table_body . "</tr>";
    }
}

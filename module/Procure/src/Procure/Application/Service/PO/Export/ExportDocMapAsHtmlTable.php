<?php
namespace Procure\Application\Service\PO\Export;

use Application\Domain\Util\Collection\Export\AbstractExportAsHtmlTable;
use Procure\Application\DTO\Po\PoDocMapDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ExportDocMapAsHtmlTable extends AbstractExportAsHtmlTable
{

    protected function createHeaderCell()
    {
        return "
        <td><b>Doc Type</b></td>
        <td><b>Doc Numner</b></td>
        <td><b>Doc Date</b></td>
        <td><b>Doc Amount</b></td>
        <td><b>Doc Curr</b></td>
        <td><b>Action</b></td>";
    }

    protected function createRowCell($element)
    {
        /**@var PoDocMapDTO $element ;*/
        $cells = '';
        $showUrl = '';
        $sysNumber = '';

        switch ($element->getDocType()) {
            case "AP":
                $showUrl = \sprintf("<a target=\"blank\" href=\"/procure/ap/view?entity_id=%s&entity_token=%s\">Show</a>", $element->getDocId(), $element->getDocToken());
                $sysNumber = \sprintf("<a target=\"blank\" href=\"/procure/ap/view?entity_id=%s&entity_token=%s\">%s</a>", $element->getDocId(), $element->getDocToken(), $element->getDocSysNumber());
                break;
            case "POGR":
                $showUrl = \sprintf("<a target=\"blank\" href=\"/procure/gr/view?entity_id=%s&entity_token=%s\">Show</a>", $element->getDocId(), $element->getDocToken());
                $sysNumber = \sprintf("<a target=\"blank\" href=\"/procure/gr/view?entity_id=%s&entity_token=%s\">%s</a>", $element->getDocId(), $element->getDocToken(), $element->getDocSysNumber());
        }

        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getDocType()));
        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($sysNumber));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocPostingDate()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocNetAmount()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocCurrency()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}


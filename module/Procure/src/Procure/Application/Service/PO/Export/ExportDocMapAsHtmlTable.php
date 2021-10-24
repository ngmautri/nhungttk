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
        $showUrl = \sprintf("<a href=\"/inventory/item-variant/view?id=%s\">Show</a>", $element->getDocId());

        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getDocType()));
        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getDocSysNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocPostingDate()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocNetAmount()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getDocCurrency()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}


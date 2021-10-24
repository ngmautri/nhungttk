<?php
namespace Inventory\Application\Service\ItemVariant\Export;

use Application\Domain\Util\Collection\Export\AbstractExportAsHtmlTable;
use Inventory\Domain\Item\Variant\GenericVariant;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ExportAsHtmlTable extends AbstractExportAsHtmlTable
{

    protected function createHeaderCell()
    {
        return "
        <td><b>Item</b></td>
        <td><b>Sys No.</b></td>
        <td><b>Attribute</b></td>
        <td><b>Action</b></td>";
    }

    protected function createRowCell($element)
    {
        /**@var GenericVariant $element ;*/
        $cells = '';
        $showUrl = \sprintf("<a href=\"/inventory/item-variant/view?id=%s\">Show</a>", $element->getId());

        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getItemName()));
        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getSysNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getFullCombinedName()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}


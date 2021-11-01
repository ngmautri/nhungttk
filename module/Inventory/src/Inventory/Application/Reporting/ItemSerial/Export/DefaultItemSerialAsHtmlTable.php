<?php
namespace Inventory\Application\Service\Reporting\ItemSerial;

use Application\Domain\Util\Collection\Export\AbstractExportAsHtmlTable;
use Inventory\Domain\Item\Serial\SerialSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemSerialAsHtmlTable extends AbstractExportAsHtmlTable
{

    protected function createHeaderCell()
    {
        return "
        <td><b>Invoice Number</b></td>
        <td><b>No.</b></td>
        <td><b>No2</b></td>
        <td><b>No3</b></td>       
        <td><b>Action</b></td>";
    }

    protected function createRowCell($element)
    {
        if (! $element instanceof SerialSnapshot) {
            return null;
        }

        $cells = '';
        $showUrl = '';
        $sysNumber = '';

        $format = "/inventory/item-serial/edit?token=%s&entity_id=%s";
        $href = sprintf($format, $element->getToken(), $element->getId());
        $format = '<a target="_blank" href="%s">Show</a>';
        $showUrl = sprintf($format, $href);

        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getInvoiceSysNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getSerialNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getSerialNumber1()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getSysNumber()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}


<?php
namespace Procure\Application\Service\PR\RowCollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsHtmlTable;
use Inventory\Domain\Item\Serial\GenericSerial;
use Procure\Domain\PurchaseRequest\PRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRowRenderAsHtmlTable extends AbstractRenderAsHtmlTable
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

    protected function createRowCell($ele)
    {
        if (! $ele instanceof GenericSerial) {
            return null;
        }

        /**
         *
         * @var PRRow $element
         */
        $element = $ele->makeSnapshot();

        $cells = '';
        $showUrl = '';
        $sysNumber = '';

        $format = "/inventory/item-serial/edit?token=%s&entity_id=%s";
        $href = sprintf($format, $element->getToken(), $element->getId());
        $format = '<a target="_blank" href="%s">Show</a>';
        $showUrl = sprintf($format, $href);

        $cells = $cells . sprintf("<td>%s</td>\n", \ucwords($element->getItemName()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getRowIdentifer()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getCreatedOn()));
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getVendorItemCode()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);
        return $cells;
    }
}


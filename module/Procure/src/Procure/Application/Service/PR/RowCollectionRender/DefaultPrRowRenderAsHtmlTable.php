<?php
namespace Procure\Application\Service\PR\RowCollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsHtmlTable;
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
        <td><b>Status</b></td>
        <td><b>No.</b></td>
        <td><b>No.</b></td>
        <td><b>No2</b></td>
        <td><b>No2</b></td>
        <td><b>Action</b></td>";
    }

    protected function createRowCell($ele)
    {
        if (! $ele instanceof PRRow) {
            return null;
        }

        /**
         *
         * @var PRRow $element
         */
        $element = $ele->makeSnapshot();

        $element = $this->getFormatter()->format($element);

        $cells = '';
        $showUrl = '';

        $format = "/procure/item-serial/edit?token=%s&entity_id=%s";
        $href = sprintf($format, $element->getToken(), $element->getId());
        $format = '<a target="_blank" href="%s">Show</a>';
        $showUrl = sprintf($format, $href);
        $cells = $cells . sprintf("<td>%s</td>\n", $element->getTransactionStatus());
        $cells = $cells . sprintf("<td>%s</td>\n", $element->getItemName());
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getRowIdentifer()));
        $cells = $cells . sprintf("<td>%s</td>\n", $element->getConvertedStandardQuantity());
        $cells = $cells . sprintf("<td>%s</td>\n", \strtoupper($element->getVendorItemCode()));
        $cells = $cells . sprintf("<td>%s</td>\n", $showUrl);

        return $cells;
    }
}


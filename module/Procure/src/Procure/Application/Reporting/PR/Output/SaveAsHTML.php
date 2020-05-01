<?php
namespace Procure\Application\Reporting\PR\Output;

use Procure\Application\Service\Output\AbstractRowsSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsHTML extends AbstractRowsSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        $table = '
<table id="mytable26" class="table table-bordered table-hover">
	<thead>
		<tr>
			<td><b>#</b></td>
			<td><b>PR number</b></td>
			<td><b>Pr Date</b></td>
	       <td><b>SKU</b></td>
            <td><b>Item Name</b></td>
			<td><b>PR<br>Q\'ty</b></td>
			<td><b>PO<br>Q\'ty</b></td>
            <td><b>Posted<br>PO<br>Q\'ty</b></td>
            
            <td><b>GR<br>Q\'ty</b></td>
            <td><b>Posted<br>GR<br>Q\'ty</b></td>
            
            <td><b>Stock<br>GR<br>Q\'ty</b></td>
            <td><b>Posted<br>Stock<br>GR<br>Q\'ty</b></td>
            
            <td><b>AP<br>Q\'ty</b></td>
            <td><b>Posted <br>AP<br>Q\'ty</b></td>
			<td><b>Action</b></td>
		</tr>
	</thead>
	<tbody>
%s
    </tbody>
</table>
';

        $bodyHtml = '';
        $n = 0;

        foreach ($rows as $row) {

            /**@var PRRowSnapshot $row ;*/

            $n ++;

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $this->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPr()->getPrName());

            if ($row->getPr()->getSubmittedOn() !== null) {
                $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", date_format($row->getPr()->getSubmittedOn(), "d-m-Y"));
            } else {
                $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", "");
            }
            // $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $a['pr_year']);
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getItem()->getItemSku());

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $a['item_name']);
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['po_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['posted_po_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['gr_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['posted_gr_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['stock_gr_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['posted_stock_gr_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['ap_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", number_format($a['posted_ap_qty'], 2));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", "");

            $bodyHtml = $bodyHtml . "</tr>";
        }
        return sprintf($table, $bodyHtml);
    }
}

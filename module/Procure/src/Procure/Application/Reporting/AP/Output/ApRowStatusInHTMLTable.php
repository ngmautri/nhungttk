<?php
namespace Procure\Application\Reporting\AP\Output;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowStatusInHTMLTable extends ApRowStatusOutputStrategy
{

    private $limit;

    private $offset;

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::createOutput()
     */
    public function createOutput($result)
    {
        if (count($result) == 0)
            return null;

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

        foreach ($result as $a) {

            /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
            $pr_row_entity = $a[0];

            if ($pr_row_entity->getPr() == null || $pr_row_entity->getItem() == null) {
                continue;
            }

            $n ++;

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $this->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $pr_row_entity->getPr()->getPrName());

            if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", date_format($pr_row_entity->getPr()->getSubmittedOn(), "d-m-Y"));
            } else {
                $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", "");
            }
            // $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $a['pr_year']);
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $pr_row_entity->getItem()->getItemSku());

            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $a['item_name']);
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $pr_row_entity->getQuantity());
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

    /**
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     *
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}

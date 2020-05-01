<?php
namespace Procure\Application\Reporting\PR\Output;

use Procure\Application\Service\Output\Contract\RowsSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsHTML implements RowsSaveAsInterface
{

    private $limit;

    private $offset;

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

            $row = $formatter->format($row);

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $this->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPrNumber());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getItemSKU());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getItemName());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPostedPoQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPostedGrQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPostedStockQrQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPostedApQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", "");

            $bodyHtml = $bodyHtml . "</tr>";
        }
        return sprintf($table, $bodyHtml);
    }
}

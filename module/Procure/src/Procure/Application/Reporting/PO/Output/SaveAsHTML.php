<?php
namespace Procure\Application\Reporting\PO\Output;

use Procure\Application\Service\Output\Contract\RowsSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

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

    private $totalRecords;

    /**
     *
     * @return mixed
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     *
     * @param mixed $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        if ($rows == null) {
            return;
        }

        $tmp = sprintf('Record %s to %s of %s found!', $this->getOffset() + 1, $this->getOffset() + count($rows), $this->getTotalRecords());
        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $tmp);

        $table = $result_msg . '
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

            /**@var PORowSnapshot $row ;*/

            $n ++;

            $row = $formatter->format($row);

            $row->itemName1 = html_entity_decode($row->getItemName1());

            $bodyHtml = $bodyHtml . "<tr>\n";
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $n + $this->getOffset());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPrNumber());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getItemSKU());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getItemName());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getQuantity());
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", $row->getPostedGrQuantity());
            // $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", \json_encode($row, JSON_UNESCAPED_UNICODE));
            $bodyHtml = $bodyHtml . sprintf("<td>%s</td>\n", "");

            $bodyHtml = $bodyHtml . "</tr>";
        }
        return sprintf($table, $bodyHtml);
    }
}

<?php
namespace Procure\Application\Service\PR\Output;

use Procure\Application\Service\Output\AbstractDocSaveAsPdf;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsPdf extends AbstractDocSaveAsPdf
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (! $doc instanceof PRDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        // Set Header
        $params = [
            "docNumber" => $doc->getSysNumber(),
            "doc" => $doc
        ];
        $this->getBuilder()->buildHeader($params);
        $html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>

 .docType{
        color: black;
        font-family: times;
        font-weight: 200;  
        font-size: 16pt;
        text-decoration: underline;
        margin-bottom:20pt;
    }
    
    
    
.table-fill {
  background: white;
  border-radius:3px;
  border-collapse: collapse;
  margin: auto;
  max-width: 1600px;
  padding:5px;
  width: 100%;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  animation: float 5s infinite;
}

th {
  border-bottom: 1px solid #C1C3D1;
  border-top: 1px solid #C1C3D1;
  background:#1b1e24;
  font-size:12pt;
  font-weight: 100;
  padding:24px;
  text-align:left;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  vertical-align:middle;
}



tr {
  border-top: 1px solid #C1C3D1;
  border-bottom-: 1px solid #C1C3D1;
  color:black;
  font-size:12pt;
  font-weight:normal;
  text-shadow: 0 1px 1px rgba(256, 256, 256, 0.1);
  margin-top:30px;
  margin-bottom:30px;
  
}

tr:hover td {
  background:#4E5066;
  color:#FFFFFF;
  border-top: 1px solid #22262e;
}

tr:first-child {
  border-top:none;
}

tr:last-child {
  border-bottom:none;
}

tr:nth-child(odd) td {
  background:#EBEBEB;
}

tr:nth-child(odd):hover td {
  background:#4E5066;
}

tr:last-child td:first-child {
  border-bottom-left-radius:3px;
}

tr:last-child td:last-child {
  border-bottom-right-radius:3px;
}

td {
  background:#FFFFFF;
   text-align:left;
  vertical-align:middle;
  font-weight:100;
  font-size:10pt;
  border-bottom: 1px solid #C1C3D1;
       font-family: times;

}

td:last-child {
  border-right: 0px;
}

th.text-left {
  text-align: left;
}

th.text-center {
  text-align: center;
}

th.text-right {
  text-align: right;
}

td.text-left {
  text-align: left;
}

td.text-center {
  text-align: center;
}

td.text-right {
  text-align: right;
}

.docDetail{
    color: graytext; text-align: Left;
    padding:0;
    margin:0;
    font-size:9pt;
     font-weight:normal;
 color: black;
       font-family: times;

font-style: italic;
 
}

.itemDetail{
     color: #4D4B4B ;
     font-size:11pt;
     font-weight:normal;
     font-family: monospace;
     font-style: italic;
     margin-left:9.5pt;
 
}


</style>

EOF;

        $header = $html . '<div class="docType">Purchase Request</div><br>';
        $header = $header . \sprintf('<span class="docDetail">No.         : %s - Date: %s</span><br>', ucfirst($doc->getDocNumber()), $doc->getSubmittedOn());
        $header = $header . \sprintf('<span class="docDetail">Ref.        : %s</span><br>', $doc->getSysNumber());
        $header = $header . \sprintf('<span class="docDetail">Created by  : %s</span><br>', $doc->getCreatedByName());

        $details = $html . '<table  class="table-fill">
        <tr class="text-left" style="color:black;">
        <th class="text-left" style="width: 30px;">#</th>
        <th class="text-left" style="width: 40%;">Item</th>
        <th class="text-left" style="width: 50px;">Unit</th>
        <th class="text-left" style="width: 40px;">Qty</th>
        <th class="text-left">UP</th>
        <th class="text-left">Net</th>
        </tr>';
        $n = 0;

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );
        foreach ($doc->getDocRows() as $r) {

            $n ++;

            /**
             *
             * @var PRRowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $itemDetails = \sprintf('<span class="itemDetail">Id: %s</span>', $row->getItemSKU());

            if ($row->getItemManufacturerCode() !== null) {
                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">Code: %s</span>', $row->getItemManufacturerCode());
            }

            if ($row->getLastCurrency() !== null) {

                if (in_array($row->getLastCurrency(), $curency)) {
                    $decimalNo = 2;
                }

                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">%s</span>', $row->getLastCurrency());
            }

            if ($row->getLastUnitPrice() !== null) {
                $itemDetails = $itemDetails . \sprintf('<span class="itemDetail"  style="font-size:9.5pt;"> %s</span>', number_format($row->getLastUnitPrice(), $decimalNo));
            }

            if ($row->getLastVendorName() !== null) {
                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail" style="font-size:9.5pt;">%s</span>', $row->getLastVendorName());
            }

            // $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">Code: %s</span>', $row->getItemSysNumber());

            $details .= '<tr style="font-size: 9.5px; border: 0px solid black;">';
            $details .= sprintf('<td class="text-left">%s<br></td>', $n);
            $details .= sprintf('<td class="text-left"><div>%s</div>%s</td>', strtoupper($row->getItemName()), $itemDetails);
            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocUnit());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocQuantity());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocUnitPrice());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getNetAmount());
            $details .= '</tr>';
        }

        $details .= '</table>';

        $params = [
            "doc" => $doc,
            "header" => $header,
            "details" => $details
        ];
        $this->getBuilder()->buildBody($params);

        // created footer and export
        $this->getBuilder()->buildFooter();
    }
}

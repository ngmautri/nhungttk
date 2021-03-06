<?php
namespace Inventory\Application\Service\Transaction\Output;

use Inventory\Application\Export\Transaction\AbstractDocSaveAsPdf;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxSaveAsPdf extends AbstractDocSaveAsPdf
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

        if (! $doc instanceof GenericTrx) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        // take long time
        set_time_limit(2500);

        // Set Header
        $params = [
            "docNumber" => $doc->getSysNumber(),
            "doc" => $doc
        ];
        $this->getBuilder()->buildHeader($params);

        $html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>

  h1 {
        color: navy;
        font-family: times;
        font-size: 18pt;
        text-decoration: underline;
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
  font-size:12px;
  font-weight: 100;
  padding:24px;
  text-align:left;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  vertical-align:middle;
}

th:first-child {
  border-top-left-radius:3px;
}
 
th:last-child {
  border-top-right-radius:3px;
  border-right:none;
}
  
tr {
  border-top: 1px solid #C1C3D1;
  border-bottom-: 1px solid #C1C3D1;
  color:#666B85;
  font-size:12px;
  font-weight:normal;
  text-shadow: 0 1px 1px rgba(256, 256, 256, 0.1);
  margin-top:50px;
  margin-bottom:50px;
  
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
  padding-top:50px;
  padding-bottom:50px;
  text-align:left;
  vertical-align:middle;
  font-weight:100;
  font-size:11px;  
  border-bottom: 1px solid #C1C3D1;
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

</style>



EOF;

        $docTitle = "Goods Issue";
        if ($doc->getMovementFlow() == TrxFlow::WH_TRANSACTION_IN) {
            $docTitle = "Goods Receipt";
        }

        $details = $html . \sprintf('<div class=""><h1 class="title" style="text-align: center">%s</h1></div>', $docTitle);

        $details .= '
        <table class="table-fill">
        <tr style="font-size: 9.5px; border: 0.5px solid black;">
        <th style="width: 30px; border: 0.5px solid black;"></th>
        <th style="width: 50px; border: 0.5px solid black;">Acc</th>
        <th style="width: 50px; border: 0.5px solid black;">CC</th>
        <th style="width: 35%; border: 0.5px solid black; text-align: center;">Item</th>
        <th style="width: 50px; border: 0.5px solid black;">Unit</th>
        <th style="width: 40px; border: 0.5px solid black;">Qty</th>
        <th style="border: 0.5px solid black;">UP</th>
        <th style="border: 0.5px solid black;">Net</th>
        </tr>';

        $n = 0;
        foreach ($doc->getDocRows() as $r) {

            $n ++;

            /**
             *
             * @var PORowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $details .= '<tr style="font-size: 10px; border: 0px solid black;">';
            $details .= sprintf('<td class="text-left">%s<br></td>', $n);
            $details .= sprintf('<td class="text-left">%s</td>', $row->getGlAccountNumber() . $row->getGlAccountName());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getCostCenterName());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getItemName());
            $details .= sprintf('<td class="text-center">%s</td>', $row->getDocUnit());
            $details .= sprintf('<td class="text-right">%s</td>', $row->getDocQuantity());
            $details .= sprintf('<td class="text-right">%s</td>', $row->getDocUnitPrice());
            $details .= sprintf('<td class="text-right">%s</td>', $row->getNetAmount());
            $details .= '</tr>';
        }

        $details .= '</table>';

        $params = [
            "doc" => $doc,
            "details" => $details
        ];
        $this->getBuilder()->buildBody($params);

        // created footer and export
        $this->getBuilder()->buildFooter();
    }
}

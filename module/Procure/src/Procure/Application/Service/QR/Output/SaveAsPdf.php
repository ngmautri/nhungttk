<?php
namespace Procure\Application\Service\QR\Output;

use Procure\Application\Service\Output\AbstractDocSaveAsPdf;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
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

        if (! $doc instanceof GenericDoc) {
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

        $details = '<h3 style="text-align: center">Quotation</h3>';

        $details .= '
        <table style="font-size: 10px; border: 0.5px solid black; width:100%">
        <tr style="font-size: 9.5px; border: 0.5px solid black;">
        <td style="width: 30px; border: 0.5px solid black;">#</td>
        <td style="width: 35%; border: 0.5px solid black;">#Item</td>
        <td style="width: 30px; border: 0.5px solid black;">#Unit</td>
        <td style="width: 40px; border: 0.5px solid black;">#Qty</td>
        <td style="border: 0.5px solid black;">#UP</td>
        <td style="border: 0.5px solid black;">#Net</td>
        </tr>/';

        $n = 0;
        foreach ($doc->getDocRows() as $r) {

            $n ++;

            /**
             *
             * @var PRRowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $details .= '<tr style="font-size: 9.5px; border: 0px solid black;">';
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s<br></td>', $n);
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s</td>', $row->getItemName());
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s</td>', $row->getDocUnit());
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s</td>', $row->getDocQuantity());
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s</td>', $row->getDocUnitPrice());
            $details .= sprintf('<td style="font-size: 9.5px; solid black;">%s</td>', $row->getNetAmount());
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

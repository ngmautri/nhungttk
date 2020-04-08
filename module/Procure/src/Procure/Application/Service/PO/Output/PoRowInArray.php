<?php
namespace Procure\Application\Service\PO\Output;

use Procure\Application\Service\Output\RowOutputDecorator;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 * PR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowInArray extends RowOutputDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::formatMultiplyRows()
     */
    public function formatMultiplyRows($rows)
    {
        if (count($rows) == 0) {
            return null;
        }

        $output = array();
        foreach ($rows as $row) {
            $output[] = $this->formatRow($row);
        }

        return $output;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::createOutput()
     */
    public function createOutput(GenericDoc $doc)
    {
        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        $output = array();
        foreach ($doc->getDocRows() as $row) {
            /**
             * @var GenericRow $row ;
             */
            $output[] =  $this->formatRow($row->makeSnapshot());
        }
        return $output;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputStrategy::formatRow()
     */
    public function formatRow(RowSnapshot $row)
    {
        if (! $row instanceof RowSnapshot) {
            return null;
        }

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $row = $this->outputStrategy->formatRow($row);

        if ($row instanceof PORowSnapshot) {
            $row->billedAmount = ($row->getBilledAmount() !== null ? number_format($row->getBilledAmount(), $decimalNo) : 0);
            $row->draftAPQuantity = ($row->getDraftAPQuantity() !== null ? number_format($row->getDraftAPQuantity(), $decimalNo) : 0);
            $row->openAPAmount = ($row->getOpenAPAmount() !== null ? number_format($row->getOpenAPAmount(), $decimalNo) : 0);
            $row->postedAPQuantity = ($row->getPostedAPQuantity() !== null ? number_format($row->getPostedAPQuantity(), $decimalNo) : 0);
            $row->draftGrQuantity = ($row->getDraftGrQuantity() !== null ? number_format($row->getDraftGrQuantity(), $decimalNo) : 0);
            $row->postedGrQuantity = ($row->getPostedGrQuantity() !== null ? number_format($row->getPostedGrQuantity(), $decimalNo) : 0);
            $row->confirmedGrBalance = ($row->getConfirmedGrBalance() !== null ? number_format($row->getConfirmedGrBalance(), $decimalNo) : 0);
            $row->openGrBalance = ($row->getOpenGrBalance() !== null ? number_format($row->getOpenGrBalance(), $decimalNo) : 0);
        }
        
        return $row;
    }

}

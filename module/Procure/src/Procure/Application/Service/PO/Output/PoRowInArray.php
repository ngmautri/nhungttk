<?php
namespace Procure\Application\Service\PO\Output;

use Procure\Application\Service\Output\RowOutputDecorator;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Procure\Domain\PurchaseOrder\PORow;
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
        return $this->outputStrategy->formatMultiplyRows($rows);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::createOutput()
     */
    public function createOutput(GenericDoc $doc)
    {
        return $this->outputStrategy->createOutput($doc);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputStrategy::formatRow()
     */
    public function formatRow(GenericRow $row)
    {
        if (! $row instanceof PORow) {
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

        $dto = $this->outputStrategy->formatRow($row);

        if ($dto instanceof PORowSnapshot) {
            $dto->billedAmount = ($row->getBilledAmount() !== null ? number_format($row->getBilledAmount(), $decimalNo) : 0);
            $dto->openAPAmount = ($row->getOpenAPAmount() !== null ? number_format($row->getOpenAPAmount(), $decimalNo) : 0);
            $dto->draftAPQuantity = ($row->getDraftAPQuantity() !== null ? number_format($row->getDraftAPQuantity(), $decimalNo) : 0);
            $dto->postedAPQuantity = ($row->getPostedAPQuantity() !== null ? number_format($row->getPostedAPQuantity(), $decimalNo) : 0);
            $dto->openAPAmount = ($row->getOpenAPAmount() !== null ? number_format($row->getOpenAPAmount(), $decimalNo) : 0);
            $dto->draftGrQuantity = ($row->getDraftGrQuantity() !== null ? number_format($row->getDraftGrQuantity(), $decimalNo) : 0);
            $dto->postedGrQuantity = ($row->getPostedGrQuantity() !== null ? number_format($row->getPostedGrQuantity(), $decimalNo) : 0);
            $dto->confirmedGrBalance = ($row->getConfirmedGrBalance() !== null ? number_format($row->getConfirmedGrBalance(), $decimalNo) : 0);
            $dto->openGrBalance = ($row->getOpenGrBalance() !== null ? number_format($row->getOpenGrBalance(), $decimalNo) : 0);
        }
        
        return $dto;
    }

}

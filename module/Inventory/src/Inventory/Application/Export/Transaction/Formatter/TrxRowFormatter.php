<?php
namespace Inventory\Application\Export\Transaction\Formatter;

use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Procure\Domain\RowSnapshot;

class TrxRowFormatter extends RowFormatterDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter::format()
     */
    public function format(RowSnapshot $row)
    {
        /**
         *
         * @var TrxRowSnapshot $row ;
         */
        $trxName = '';
        $trxArray = null;
        if ($row->getFlow() == TrxFlow::WH_TRANSACTION_IN) {
            $trxArray = TrxType::getGoodReceiptTrx();
        } else {
            $trxArray = TrxType::getGoodIssueTrx();
        }

        if (isset($trxArray[$row->getTransactionType()])) {
            $trxName = $trxArray[$row->getTransactionType()]['type_name'];
        }

        $format = '<span  title="%s">%s<span>';
        $row->transactionType = \sprintf($format, $trxName, $row->transactionType);

        $row->cogsLocal = ($row->getCogsLocal() !== null ? number_format($row->getCogsLocal(), 0) : 0);
        $row->convertedStandardUnitPrice = ($row->getConvertedStandardUnitPrice() !== null ? number_format($row->getConvertedStandardUnitPrice(), 0) : 0);
        $row->stockValue = ($row->getStockValue() !== null ? number_format($row->getStockValue(), 0) : 0);

        return $row;
    }
}

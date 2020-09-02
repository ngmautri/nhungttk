<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\AbstractSaveAs;
use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxRowsSaveAsArray extends AbstractSaveAs implements RowsSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        try {

            $n = 0;
            foreach ($rows as $row) {
                $output[] = $formatter->format($row);
                $n ++;
            }
            $this->logInfo(\sprintf("TrxRowsSaveAsArray: %s formatted!", $n));
            return $output;
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }
}

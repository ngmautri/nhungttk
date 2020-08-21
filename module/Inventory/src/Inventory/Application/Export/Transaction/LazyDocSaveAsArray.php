<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\AbstractSaveAs;
use Inventory\Application\Export\Transaction\Contracts\LazyDocSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LazyDocSaveAsArray extends AbstractSaveAs implements LazyDocSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Transaction\Contracts\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericTrx $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null)
    {
        try {
            if (! $doc instanceof GenericTrx) {
                throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
            }

            if ($doc->getLazyRowsCollection()->count() == 0) {
                return;
            }

            $this->logInfo(\sprintf('LazyDocSaveAsArray %s-%s', $offset, $limit));

            $output = [];
            $n = 0;
            foreach ($doc->getLazyRowSnapshotCollection()->slice($offset, $limit) as $lazyRowSnapshot) {
                /**
                 *
                 * @todo
                 * @var TrxRowSnapshot $row ;
                 */
                $n ++;
                $rowSnapshot = $lazyRowSnapshot();
                $formattedRow = $formatter->format($rowSnapshot);
                // $this->logInfo(\sprintf($n . json_encode($formattedRow)));
                // $this->logInfo(JsonErrors::getErrorMessage(json_last_error()));
                $output[] = $formattedRow;
            }
            $this->logInfo(\sprintf("rows %s formatted!", $n));

            return $output;
        } catch (Exception $e) {
            $this->logException($e);
        }
    }
}

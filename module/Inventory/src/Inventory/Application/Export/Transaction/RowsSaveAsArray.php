<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowsSaveAsArray implements RowsSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        try {

            if (count($rows) == 0) {
                return null;
            }

            $output = array();
            foreach ($rows as $row) {
                $output[] = $formatter->format($row);
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

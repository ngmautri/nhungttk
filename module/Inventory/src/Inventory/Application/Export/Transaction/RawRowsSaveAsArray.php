<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RawRowsSaveAsArray implements RowsSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        try {

            if (count($rows) == 0) {
                return null;
            }

            $output = array();
            foreach ($rows as $r) {
                $output[] = $formatter->format($r);
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

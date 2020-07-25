<?php
namespace Inventory\Application\Export\Item;

use Inventory\Application\Export\Item\Contracts\SaveAsInterface;
use Inventory\Application\Export\Item\Formatter\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsArray implements SaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Item\Contracts\SaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractFormatter $formatter)
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

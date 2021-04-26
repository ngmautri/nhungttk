<?php
namespace Inventory\Application\Export\Item;

use Inventory\Application\Export\Item\Contracts\SaveAsInterface;
use Inventory\Application\Export\Item\Formatter\AbstractFormatter;
use Inventory\Infrastructure\Mapper\ItemMapper;

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

            $output = [];
            foreach ($rows as $row) {
                $snapshot = ItemMapper::createSnapshot($row);
                $output[] = $formatter->format($snapshot);
            }

            return $output;
        } catch (\Exception $e) {
            // echo $e->getTraceAsString();
        }
    }
}

<?php
namespace Procure\Application\Service\Output;

use Procure\Application\Service\Output\Contract\RowsSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Application\Service\Output\Formatter\NullRowFormatter;

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

            if ($formatter instanceof NullRowFormatter) {
                return $rows;
            }

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

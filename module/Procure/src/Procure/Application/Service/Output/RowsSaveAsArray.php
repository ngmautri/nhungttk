<?php
namespace Procure\Application\Service\Output;

use Procure\Application\Service\Output\Contract\RowsSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Application\Service\Output\Formatter\NullRowFormatter;
use Procure\Domain\GenericRow;

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

            if ($rows == null) {
                return null;
            }

            $output = [];
            foreach ($rows as $row) {

                /**
                 *
                 * @var GenericRow $row ;
                 */

                $row->updateRowStatus(); // important
                $output[] = $formatter->format($row->makeSnapshot());
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

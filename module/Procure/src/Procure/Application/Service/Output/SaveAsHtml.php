<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;

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
     * @see \Procure\Application\Service\Output\SaveAsInterface::saveMultiplyRowsAs()
     */
    public function saveMultiplyRowsAs($rows, AbstractRowFormatter $formatter)
    {
        if (count($rows) == 0) {
            return null;
        }

        $output = array();
        foreach ($rows as $row) {
            $output[] = $formatter->format($row);
        }
        return $output;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\SaveAsInterface::saveDocAs()
     */
    public function saveDocAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        $output = array();
        foreach ($doc->getDocRows() as $row) {
            /**
             *
             * @var GenericRow $row ;
             */
            $output[] = $formatter->format($row->makeSnapshot());
        }
        return $output;
    }
}

<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\DocSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DocSaveAsArray implements DocSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Transaction\Contracts\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter)
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

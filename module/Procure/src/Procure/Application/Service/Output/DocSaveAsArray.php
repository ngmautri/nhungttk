<?php
namespace Procure\Application\Service\Output;

use Procure\Application\Service\Output\Contract\DocSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
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
     * @see \Procure\Application\Service\Output\Contract\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getRowsGenerator()) == null) {
            return;
        }

        $output = array();
        foreach ($doc->getRowsGenerator() as $row) {

            /**
             *
             * @var GenericRow $row ;
             */
            $row->updateRowStatus();
            $output[] = $formatter->format($row->makeSnapshot());
        }
        return $output;
    }
}

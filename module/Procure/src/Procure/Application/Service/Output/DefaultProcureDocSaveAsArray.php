<?php
namespace Procure\Application\Service\Output;

use Procure\Application\Service\Output\Contract\ProcureDocSaveAsInterface;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultProcureDocSaveAsArray extends AbstractProcureDocSaveAs implements ProcureDocSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\ProcureDocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null)
    {
        try {
            if (! $doc instanceof GenericDoc) {
                throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
            }

            // important
            $doc->refreshDoc();

            if ($doc->getRowCollection()->count() == 0) {
                return;
            }

            $this->logInfo(\sprintf('ProcureDocSaveAsArray %s-%s', $offset, $limit));

            $output = [];
            $n = 0;

            foreach ($doc->getRowCollection()->slice($offset, $limit) as $row) {
                /**
                 *
                 * @todo
                 * @var GenericRow $row ;
                 */
                $n ++;
                $formattedRow = $formatter->format($row->makeSnapshot());
                $output[] = $formattedRow;
            }
            $this->logInfo(\sprintf("rows %s formatted!", $n));

            return $output;
        } catch (Exception $e) {
            $this->logException($e);
        }
    }
}

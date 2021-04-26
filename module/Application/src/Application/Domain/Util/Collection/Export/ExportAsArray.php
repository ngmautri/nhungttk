<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Domain\Util\Collection\GenericCollection;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsArray extends AbstractExport
{

    public function execute(GenericCollection $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
    {
        if ($collection->isEmpty()) {
            return "Nothing found!";
        }

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        if ($filter == null) {
            $filter = new DefaultFilter();
        }

        $output = [];
        foreach ($collection as $element) {

            $output[] = $formatter->format($element->makeSnapshot());
        }

        return $output;
    }
}


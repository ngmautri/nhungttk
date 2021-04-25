<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Domain\Util\Collection\GenericCollection;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Formatter\NullFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsArray extends AbstractExport
{

    public function execute(GenericCollection $collection, FilterInterface $filter, ElementFormatterInterface $formatter)
    {
        if ($collection->isEmpty()) {
            return "Nothing found!";
        }

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        $output = [];
        foreach ($collection as $element) {
            $element = $formatter->format($element);
            $output[] = $element;
        }

        return $output;
    }
}


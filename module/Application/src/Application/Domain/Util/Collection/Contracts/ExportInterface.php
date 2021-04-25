<?php
namespace Application\Domain\Util\Collection\Contracts;

use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ExportInterface
{

    public function execute(GenericCollection $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null);
}


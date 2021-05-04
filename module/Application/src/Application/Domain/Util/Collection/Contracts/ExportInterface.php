<?php
namespace Application\Domain\Util\Collection\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ExportInterface
{

    public function execute(\Traversable $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null);
}


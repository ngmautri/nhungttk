<?php
namespace Application\Domain\Util\Collection\Contracts;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ExportInterface
{

    public function execute(ArrayCollection $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null);
}


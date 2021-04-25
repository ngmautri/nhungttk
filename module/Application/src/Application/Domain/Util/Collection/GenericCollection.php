<?php
namespace Application\Domain\Util\Collection;

use Application\Domain\Util\Collection\Export\AbstractExport;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericCollection extends ArrayCollection
{

    public function export(AbstractExport $export)
    {
        if ($export == null) {
            throw new \InvalidArgumentException("ExportInterface not found");
        }
    }
}


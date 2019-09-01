<?php
namespace Procure\Application\Service\PO;

use Application\Service\AbstractService;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Application\Service\PO\Output\PoRowOutputStrategy;
use Procure\Application\Service\PO\Output\PoRowInArray;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POService extends AbstractService
{

    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPODetailsById($id, $outputStrategy = null)
    {
        $poRepo = new DoctrinePOQueryRepository($this->getDoctrineEM());
        $po = $poRepo->getFullDetailsById($id);

        if ($po == null) {
            return null;
        }

        $factory = null;
        switch ($outputStrategy) {
            case PoRowOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PoRowInArray();
                break;
            default:
                $factory = new PoRowInArray();
                break;
        }

        if ($factory !== null) {
            $output = $factory->createOutput($po->getDocRows());
            $po->setRowsOutput($output);
        }

        return $po;
    }
}

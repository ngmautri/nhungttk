<?php
namespace Procure\Application\Service\PO;

use Application\Service\AbstractService;
use Procure\Application\Service\PO\Output\PoRowInArray;
use Procure\Application\Service\PO\Output\PoRowOutputStrategy;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Application\Service\PO\Output\PoRowInExcel;

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
        $po = $poRepo->getPODetailsById($id);

        if ($po == null) {
            return null;
        }

        $factory = null;
        switch ($outputStrategy) {
            case PoRowOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PoRowInArray();
                break;
            case PoRowOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new PoRowInExcel();
                break;

            default:
                $factory = new PoRowInArray();
                break;
        }

        if ($factory !== null) {
            $output = $factory->createOutput($po);
            $po->setRowsOutput($output);
        }

        return $po;
    }
}

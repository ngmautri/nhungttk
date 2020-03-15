<?php
namespace Procure\Application\Service\PO;

use Application\Service\AbstractService;
use Procure\Application\Service\PO\Output\PoRowInArray;
use Procure\Application\Service\PO\Output\PoRowInExcel;
use Procure\Application\Service\PO\Output\PoRowOutputStrategy;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POService extends AbstractService
{

    private $cmdRepository;

    private $queryRepository;

    /**
     *
     * @param int $target_id
     * @param string $target_token
     * @param int $entity_id
     * @param string $entity_token
     */
    public function getPOofRow($target_id, $target_token, $entity_id, $entity_token)
    {
        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $po = $this->getQueryRepository()->getPODetailsById($target_id, $target_token);

        if (! $po == null) {
            $rootEntity = $po;
            $rootDTO = $po->makeDTOForGrid();

            $localEntity = $po->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity !== null) {
                $localDTO = $localEntity->makeDTOForGrid();
            }
        }

        return [
            "rootEntity" => $rootEntity,
            "localEntity" => $localEntity,
            "rootDTO" => $rootDTO,
            "localDTO" => $localDTO
        ];
    }

    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPODetailsById($id, $token = null, $outputStrategy = null)
    {
        $po = $this->getQueryRepository()->getPODetailsById($id, $token);

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

    /**
     *
     * @param int $id
     * @param string $token
     * @return NULL|void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public function getPO($id, $token = null)
    {
        return $this->getQueryRepository()->getPODetailsById($id, $token);
    }

    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPOHeaderById($id, $token = null)
    {
        $po = $this->getQueryRepository()->getHeaderById($id);

        if ($po == null) {
            return null;
        }
        return $po->makeHeaderDTO();
    }

    // ======================================================================

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }

    /**
     *
     * @param DoctrinePOCmdRepository $cmdRepository
     */
    public function setCmdRepository(DoctrinePOCmdRepository $cmdRepository)
    {
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }

    /**
     *
     * @param DoctrinePOQueryRepository $queryRepository
     */
    public function setQueryRepository(DoctrinePOQueryRepository $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }
}

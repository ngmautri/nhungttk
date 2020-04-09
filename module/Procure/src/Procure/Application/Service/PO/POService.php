<?php
namespace Procure\Application\Service\PO;

use Application\Service\AbstractService;
use Procure\Application\Service\Output\RowNumberFormatter;
use Procure\Application\Service\Output\RowTextAndNumberFormatter;
use Procure\Application\Service\Output\SaveAsArray;
use Procure\Application\Service\Output\SaveAsSupportedType;
use Procure\Application\Service\PO\Output\PoRowFormatter;
use Procure\Application\Service\PO\Output\PoSaveAsExcel;
use Procure\Application\Service\PO\Output\PoSaveAsOpenOffice;
use Procure\Application\Service\PO\Output\Spreadsheet\PoExcelBuilder;
use Procure\Application\Service\PO\Output\Spreadsheet\PoOpenOfficeBuilder;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Application\Service\PO\Output\Pdf\PoPdfBuilder;
use Procure\Application\Service\PO\Output\PoSaveAsPdf;

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
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new PoRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new PoExcelBuilder();
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new PoSaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new PoOpenOfficeBuilder();
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new PoSaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new PoPdfBuilder();
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new PoSaveAsPdf($builder);
                break;

            default:
                $formatter = new PoRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveDocAs($po, $formatter);
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
        return $this->getQueryRepository()->getHeaderById($id, $token);
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

<?php
namespace Inventory\Application\Service\Transaction;

use Application\Service\AbstractService;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Application\Export\Transaction\DocSaveAsArray;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Export\Transaction\Formatter\RowNumberFormatter;
use Inventory\Application\Export\Transaction\Formatter\RowTextAndNumberFormatter;
use Inventory\Application\Service\Transaction\Output\TrxRowFormatter;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsExcel;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsOpenOffice;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsPdf;
use Inventory\Application\Service\Transaction\Output\Pdf\TrxPdfBuilder;
use Inventory\Application\Service\Transaction\Output\Spreadsheet\TrxExcelBuilder;
use Inventory\Application\Service\Transaction\Output\Spreadsheet\TrxOpenOfficeBuilder;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxService extends AbstractService
{

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getDocHeaderByTokenId($id, $token)
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @param string $outputStrategy
     * @return NULL|\Inventory\Domain\Transaction\TrxDoc
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null)
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof TrxDoc) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new TrxRowFormatter(new RowTextAndNumberFormatter());
                $factory = new DocSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new TrxExcelBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new TrxOpenOfficeBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new TrxPdfBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsPdf($builder);
                break;

            default:
                $formatter = new TrxRowFormatter(new RowTextAndNumberFormatter());
                $factory = new DocSaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
        }

        return $rootEntity;
    }

    /**
     *
     * @param int $target_id
     * @param string $target_token
     * @param int $entity_id
     * @param string $entity_token
     * @return NULL[]|object[]|\Inventory\Domain\Transaction\TrxRow[]|\Inventory\Domain\Transaction\TrxDoc[]
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token)
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid(new TrxDTO());

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof TrxRow) {
                $localDTO = $localEntity->makeDetailsDTO();
            }
        }

        return [
            "rootEntity" => $rootEntity,
            "localEntity" => $localEntity,
            "rootDTO" => $rootDTO,
            "localDTO" => $localDTO
        ];
    }
}

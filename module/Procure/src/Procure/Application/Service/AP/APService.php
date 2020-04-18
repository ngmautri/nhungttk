<?php
namespace Procure\Application\Service\AP;

use Application\Service\AbstractService;
use Procure\Application\Service\AP\Output\ApRowFormatter;
use Procure\Application\Service\AP\Output\ApSaveAsExcel;
use Procure\Application\Service\AP\Output\ApSaveAsOpenOffice;
use Procure\Application\Service\AP\Output\ApSaveAsPdf;
use Procure\Application\Service\AP\Output\Pdf\ApPdfBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApExcelBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApOpenOfficeBuilder;
use Procure\Application\Service\Output\RowNumberFormatter;
use Procure\Application\Service\Output\RowTextAndNumberFormatter;
use Procure\Application\Service\Output\SaveAsArray;
use Procure\Application\Service\Output\SaveAsSupportedType;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 * AP Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APService extends AbstractService
{

    /**
     * '
     *
     * @param int $id
     * @param string $token
     */
    public function getDocHeaderByTokenId($id, $token)
    {
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @param string $outputStrategy
     * @return NULL|\Procure\Domain\AccountPayable\APDoc
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null)
    {
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof APDoc) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new ApRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ApExcelBuilder();
                $formatter = new ApRowFormatter(new RowNumberFormatter());
                $factory = new ApSaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new ApOpenOfficeBuilder();
                $formatter = new ApRowFormatter(new RowNumberFormatter());
                $factory = new ApSaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new ApPdfBuilder();
                $formatter = new ApRowFormatter(new RowNumberFormatter());
                $factory = new ApSaveAsPdf($builder);
                break;

            default:
                $formatter = new ApRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveDocAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
        }

        return $rootEntity;
    }

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token)
    {
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid();

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

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
}

<?php
namespace Procure\Application\Service\QR;

use Application\Service\AbstractService;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Application\Service\Output\RowNumberFormatter;
use Procure\Application\Service\Output\RowTextAndNumberFormatter;
use Procure\Application\Service\Output\SaveAsArray;
use Procure\Application\Service\Output\SaveAsSupportedType;
use Procure\Application\Service\QR\Output\RowFormatter;
use Procure\Application\Service\QR\Output\SaveAsExcel;
use Procure\Application\Service\QR\Output\SaveAsOpenOffice;
use Procure\Application\Service\QR\Output\SaveAsPdf;
use Procure\Application\Service\QR\Output\Pdf\PdfBuilder;
use Procure\Application\Service\QR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\QR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

/**
 * QR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRService extends AbstractService
{

    public function getDocHeaderByTokenId($id, $token)
    {
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null)
    {
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof QRDoc) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new RowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new RowFormatter(new RowNumberFormatter());
                $factory = new SaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new RowFormatter(new RowNumberFormatter());
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new PdfBuilder();
                $formatter = new RowFormatter(new RowNumberFormatter());
                $factory = new SaveAsPdf($builder);
                break;

            default:
                $formatter = new RowFormatter(new RowTextAndNumberFormatter());
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
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid(new QrDTO());

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof QRRow) {
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

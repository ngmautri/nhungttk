<?php
namespace Procure\Application\Service\QR;

use Application\Service\AbstractService;
use Procure\Application\Service\Contracts\ProcureServiceInterface;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\QR\Output\QrRowFormatter;
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
class QRService extends AbstractService implements ProcureServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocHeaderByTokenId()
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
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
                $f = new RowTextAndNumberFormatter();
                $f->setLocale($locale);
                $formatter = new QrRowFormatter($f);
                $factory = new DocSaveAsArray();

            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new QrRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new QrRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new PdfBuilder();
                $formatter = new QrRowFormatter(new RowTextAndNumberFormatter());
                $factory = new SaveAsPdf($builder);
                break;

            default:
                $f = new RowTextAndNumberFormatter();
                $f->setLocale($locale);
                $formatter = new QrRowFormatter($f);
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
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getRootEntityOfRow()
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid();

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity !== null) {
                $localDTO = $localEntity->makeDetailsDTO();
                $f = new RowNumberFormatter();
                $f->setLocale($locale);
                $formatter = new QrRowFormatter($f);
                $localDTO = $formatter->format($localDTO);
            }
        }

        return [
            "rootEntity" => $rootEntity,
            "localEntity" => $localEntity,
            "rootDTO" => $rootDTO,
            "localDTO" => $localDTO
        ];
    }

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}
}

<?php
namespace Procure\Application\Service\PO;

use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\Contracts\PoServiceInterface;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\PO\Output\PoRowFormatter;
use Procure\Application\Service\PO\Output\PoSaveAsExcel;
use Procure\Application\Service\PO\Output\PoSaveAsOpenOffice;
use Procure\Application\Service\PO\Output\PoSaveAsPdf;
use Procure\Application\Service\PO\Output\Pdf\PoPdfBuilder;
use Procure\Application\Service\PO\Output\Spreadsheet\PoExcelBuilder;
use Procure\Application\Service\PO\Output\Spreadsheet\PoOpenOfficeBuilder;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class POService extends AbstractService implements PoServiceInterface
{

    private $cmdRepository;

    private $queryRepository;

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenIdFromDB()
     */
    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
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
                $factory = new DocSaveAsArray();
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
                $factory = new DocSaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveAs($po, $formatter);
            $po->setRowsOutput($output);
        }

        return $po;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {
        return $this->getPODetailsById($id, $token, $outputStrategy, $locale);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocHeaderByTokenId()
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        return $this->getQueryRepository()->getHeaderById($id, $token);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getRootEntityOfRow()
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {
        return $this->getPOofRow($target_id, $target_token, $entity_id, $entity_token, $locale);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByIdFromDB()
     */
    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}

    /**
     *
     * @param int $id
     * @param string $token
     * @param CommandOptions $options
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public function createFromQuotation($id, $token, CommandOptions $options)
    {
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());
        $sourceEntity = $rep->getRootEntityByTokenId($id, $token);
        $sharedService = SharedServiceFactory::createForPO($this->getDoctrineEM());

        $rootEntity = PODoc::createFromQuotation($sourceEntity, $options, $sharedService);
        return $rootEntity;
    }

    /**
     *
     * @param int $target_id
     * @param string $target_token
     * @param int $entity_id
     * @param string $entity_token
     */
    public function getPOofRow($target_id, $target_token, $entity_id, $entity_token, $locale)
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
                $f = new RowNumberFormatter();
                $f->setLocale($locale);
                $formatter = new PoRowFormatter($f);
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

    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPODetailsById($id, $token = null, $outputStrategy = null, $locale = 'en_EN')
    {
        $po = $this->getQueryRepository()->getPODetailsById($id, $token);

        if ($po == null) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $f = new RowTextAndNumberFormatter();
                $f->setLocale($locale);

                $formatter = new PoRowFormatter($f);
                $factory = new DocSaveAsArray();
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
                $f = new RowTextAndNumberFormatter();
                $f->setLocale($locale);

                $formatter = new PoRowFormatter($f);
                $factory = new DocSaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveAs($po, $formatter);
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
     * @return \Procure\Infrastructure\Doctrine\POQueryRepositoryImpl
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }

    /**
     *
     * @param POQueryRepositoryImpl $queryRepository
     */
    public function setQueryRepository(POQueryRepositoryImpl $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }
}

<?php
namespace Procure\Application\Service\AP;

use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\AP\Output\ApRowFormatter;
use Procure\Application\Service\AP\Output\ApSaveAsExcel;
use Procure\Application\Service\AP\Output\ApSaveAsOpenOffice;
use Procure\Application\Service\AP\Output\ApSaveAsPdf;
use Procure\Application\Service\AP\Output\Pdf\ApPdfBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApExcelBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApOpenOfficeBuilder;
use Procure\Application\Service\Contracts\ApServiceInterface;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\AccountPayable\Factory\APFactory;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;

/**
 * AP Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APService extends AbstractService implements ApServiceInterface
{

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericAP) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new ApRowFormatter(new RowTextAndNumberFormatter());
                $factory = new DocSaveAsArray();
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
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid();

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof APRow) {
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenIdFromDB()
     */
    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByIdFromDB()
     */
    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ApServiceInterface::createFromPO()
     */
    public function createFromPO($id, $token, CommandOptions $options)
    {
        $rep = new POQueryRepositoryImpl($this->getDoctrineEM());

        $po = $rep->getOpenItems($id, $token);

        if ($po == null) {
            return null;
        }
        $sharedService = SharedServiceFactory::createForAP($this->getDoctrineEM());
        $rootEntity = APFactory::createFromPo($po, $options, $sharedService);
        return $rootEntity;
    }
}

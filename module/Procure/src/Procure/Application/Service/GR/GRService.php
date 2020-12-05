<?php
namespace Procure\Application\Service\GR;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\Service\Contracts\GrServiceInterface;
use Procure\Application\Service\GR\Output\SaveAsOpenOffice;
use Procure\Application\Service\GR\Output\SaveAsPdf;
use Procure\Application\Service\GR\Output\Pdf\PdfBuilder;
use Procure\Application\Service\GR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\GR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\PR\Output\RowFormatter;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;

/**
 * GR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRService extends AbstractService implements GrServiceInterface
{

    private $queryRepository;

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {
        $rep = new GRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericGR) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new RowFormatter(new RowTextAndNumberFormatter());
                $factory = new DocSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new RowFormatter(new RowNumberFormatter());
                $factory = new \Procure\Application\Service\GR\Output\SaveAsExcel($builder);
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
                $factory = new DocSaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
        }

        return $rootEntity;
    }

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {
        $rep = new GRQueryRepositoryImpl($this->getDoctrineEM());

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

    /**
     *
     * @param GRQueryRepositoryImpl $queryRepository
     */
    public function setQueryRepository(GRQueryRepositoryImpl $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }

    /**
     *
     * @param int $target_id
     * @param string $target_token
     * @param int $entity_id
     * @param string $entity_token
     */
    public function createFromPO($id, $token, CommandOptions $options)
    {
        $rep = new POQueryRepositoryImpl($this->getDoctrineEM());

        $po = $rep->getPODetailsById($id, $token);

        $headerValidators = new HeaderValidatorCollection();

        $sharedSpecsFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $procureSpecsFactory = new ProcureSpecificationFactory($this->getDoctrineEM());
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService, $procureSpecsFactory);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $gr = GRDoc::createFromPo($po, $options, $headerValidators, $rowValidators);
        return $gr;
    }

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {}

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}
}

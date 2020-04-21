<?php
namespace Procure\Application\Service\PR;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\FXService;
use Procure\Application\Service\Output\RowNumberFormatter;
use Procure\Application\Service\Output\RowTextAndNumberFormatter;
use Procure\Application\Service\Output\SaveAsArray;
use Procure\Application\Service\Output\SaveAsSupportedType;
use Procure\Application\Service\PR\Output\RowFormatter;
use Procure\Application\Service\PR\Output\SaveAsExcel;
use Procure\Application\Service\PR\Output\SaveAsOpenOffice;
use Procure\Application\Service\PR\Output\SaveAsPdf;
use Procure\Application\Service\PR\Output\Pdf\PdfBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 * AP Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRService extends AbstractService
{

    /**
     * '
     *
     * @param int $id
     * @param string $token
     */
    public function getDocHeaderByTokenId($id, $token)
    {
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null)
    {
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof PRDoc) {
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
        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid(new ApDTO());

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

    public function createFromPO($id, $token, CommandOptions $options)
    {
        $rep = new POQueryRepositoryImpl($this->getDoctrineEM());

        $po = $rep->getPODetailsById($id, $token);

        $headerValidators = new HeaderValidatorCollection();

        $sharedSpecsFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $procureSpecsFactory = new ProcureSpecificationFactory($this->getDoctrineEM());
        $fxService = new FXService();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService, $procureSpecsFactory);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $rootEntity = APDoc::createFromPo($po, $options, $headerValidators, $rowValidators);
        return $rootEntity;
    }
}

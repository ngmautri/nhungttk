<?php
namespace Procure\Application\Service\AP;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\FXService;
use Procure\Application\Service\AP\Output\ApRowFormatter;
use Procure\Application\Service\AP\Output\ApSaveAsExcel;
use Procure\Application\Service\AP\Output\ApSaveAsOpenOffice;
use Procure\Application\Service\AP\Output\ApSaveAsPdf;
use Procure\Application\Service\AP\Output\Pdf\ApPdfBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApExcelBuilder;
use Procure\Application\Service\AP\Output\Spreadsheet\ApOpenOfficeBuilder;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;

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

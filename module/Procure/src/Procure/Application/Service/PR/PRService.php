<?php
namespace Procure\Application\Service\PR;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\DTO\Pr\PrDTO;
use Procure\Application\Service\Output\DocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\PR\Output\RowFormatter;
use Procure\Application\Service\PR\Output\SaveAsExcel;
use Procure\Application\Service\PR\Output\SaveAsOpenOffice;
use Procure\Application\Service\PR\Output\SaveAsPdf;
use Procure\Application\Service\PR\Output\Pdf\PdfBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 * PR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRService extends AbstractService
{

    const PR_KEY_CACHE = "pr_%s";

    public function getDocHeaderByTokenId($id, $token)
    {
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @param string $outputStrategy
     * @return NULL|\Procure\Domain\PurchaseRequest\PRDoc
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null)
    {
        $key = \sprintf(self::PR_KEY_CACHE, $id);

        $resultCache = $this->getCache()->getItem($key);

        /**
         *
         * @var PRDoc $rootEntity ;
         */
        $rootEntity = null;
        if ($resultCache->isHit()) {

            $cachedRootEntity = $this->getCache()
                ->getItem($key)
                ->get();
            $rootEntity = \unserialize($cachedRootEntity);
            $this->getLogger()->info(\sprintf("PR#%s (%s) got from cache!", $id, $key));
        } else {

            // Not in Cache.
            $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            if (! $rootEntity instanceof PRDoc) {
                return null;
            }

            // always save array.
            $formatter = new RowFormatter(new RowTextAndNumberFormatter());
            $factory = new DocSaveAsArray();

            $output = $factory->saveAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
            $resultCache->set(\serialize($rootEntity));
            $this->getCache()->save($resultCache);
            $this->getLogger()->info(\sprintf("PR#%s (%s) saved into cache!", $id, $key));
        }

        // FOR PDF and Excel.

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
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
        }

        if ($factory !== null && $formatter !== null) {
            $output = $factory->saveAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
        }

        return $rootEntity;
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @param string $outputStrategy
     * @return NULL|object
     */
    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null)
    {

        // Not in Cache.
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy);
    }

    /**
     *
     * @param int $id
     * @param string $outputStrategy
     * @return NULL|object
     */
    public function getDocDetailsByIdFromDB($id, $outputStrategy = null)
    {

        // Not in Cache.
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityById($id);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy);
    }

    /**
     *
     * @param object $rootEntity
     * @param string $outputStrategy
     * @return object
     */
    private function _getRootEntity($rootEntity, $outputStrategy)
    {

        // FOR PDF and Excel.
        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
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
                // always save array.
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

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token)
    {
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid(new PrDTO());

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof PRRow) {
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

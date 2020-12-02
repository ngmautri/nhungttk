<?php
namespace Procure\Application\Service\PR;

use Application\Service\AbstractService;
use Procure\Application\DTO\Pr\PrDTO;
use Procure\Application\Service\Contracts\ProcureServiceInterface;
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
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 * PR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PRService extends AbstractService implements ProcureServiceInterface
{

    const PR_KEY_CACHE = "pr_%s";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocHeaderByTokenId()
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {

        // Not in Cache.
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityById($id);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy);
    }

    public function getDocDetailsByTokenId1($id, $token, $outputStrategy = null, $locale = 'en_EN')
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
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenIdFromDB()
     */
    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
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
    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getRootEntityOfRow()
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
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
}

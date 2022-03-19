<?php
namespace Procure\Application\Service\PR;

use Application\Service\AbstractService;
use Procure\Application\Service\Contracts\PrServiceInterface;
use Procure\Application\Service\Output\DefaultProcureDocSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\PR\Output\DefaultPrSaveAsExcel;
use Procure\Application\Service\PR\Output\DefaultPrSaveAsOpenOffice;
use Procure\Application\Service\PR\Output\DefaultPrSaveAsPdf;
use Procure\Application\Service\PR\Output\PrRowFormatter;
use Procure\Application\Service\PR\Output\Pdf\PdfBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;

/**
 * PR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRServiceV1 extends AbstractService implements PrServiceInterface
{

    const PR_KEY_CACHE = "pr_%s";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocHeaderByTokenId()
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
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
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy, $locale);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenIdFromDB()
     */
    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {

        // Not in Cache.
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy, $locale);
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
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityById($id);

        if (! $rootEntity instanceof PRDoc) {
            return null;
        }

        return $this->_getRootEntity($rootEntity, $outputStrategy, $locale);
    }

    public function getDocGirdByTokenId($id, $token, $offset = null, $limit = null, $locale = 'en_EN')
    {
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericDoc) {
            return null;
        }
        $rootEntity->setRowsOutput(null);

        $formatter = new PrRowFormatter(new RowTextAndNumberFormatter());
        $factory = new DefaultProcureDocSaveAsArray();
        $factory->setLogger($this->getLogger());

        $output = $factory->saveAs($rootEntity, $formatter, $offset, $limit);
        $rootEntity->setRowsOutput($output);

        return $rootEntity;
    }

    /**
     *
     * @param object $rootEntity
     * @param string $outputStrategy
     * @return object
     */
    private function _getRootEntity($rootEntity, $outputStrategy, $locale = 'en_EN')
    {

        // FOR PDF and Excel.
        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new RowNumberFormatter();
                $factory = new DefaultPrSaveAsExcel($builder);
                $factory->setDoctrineEM($this->getDoctrineEM());

                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new RowNumberFormatter();
                $factory = new DefaultPrSaveAsOpenOffice($builder);
                $factory->setDoctrineEM($this->getDoctrineEM());

                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new PdfBuilder();
                $formatter = new RowNumberFormatter();
                $factory = new DefaultPrSaveAsPdf($builder);
                $factory->setDoctrineEM($this->getDoctrineEM());
                break;
            default:
                // always save array.
                $formatter = new PrRowFormatter(new RowTextAndNumberFormatter());
                $factory = new DefaultProcureDocSaveAsArray();
                break;
        }

        if ($factory !== null && $formatter !== null) {
            $formatter->setLocale($locale);
            $output = $factory->saveAs($rootEntity, $formatter);
            $rootEntity->setRowsOutput($output);
        }

        return $rootEntity;
    }

    public function getTotalRows($id, $token)
    {
        $key = \sprintf("total_row_%s_%s", $id, $token);

        $resultCache = $this->getCache()->getItem($key);

        if (! $resultCache->isHit()) {

            $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
            $trx = $rep->getTotl($id, $token);

            $total = $trx->getTotalRows();
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }

        return $total;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getRootEntityOfRow()
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        /**
         *
         * @var PRDoc $rootEntity ;
         */
        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {

            $rootDTO = $rootEntity->makeSnapshot();

            $localEntity = $rootEntity->getRowFromCollectionbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof PRRow) {
                $localDTO = $localEntity->makeSnapshot();
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
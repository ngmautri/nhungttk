<?php
namespace Inventory\Application\Service\Transaction;

use Application\Application\Service\Contracts\AbstractService;
use Inventory\Application\Export\Transaction\DocSaveAsArray;
use Inventory\Application\Export\Transaction\LazyDocSaveAsArray;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Export\Transaction\Formatter\RowNumberFormatter;
use Inventory\Application\Export\Transaction\Formatter\RowTextAndNumberFormatter;
use Inventory\Application\Service\Contracts\TrxServiceInterface;
use Inventory\Application\Service\Transaction\Output\TrxRowFormatter;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsExcel;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsOpenOffice;
use Inventory\Application\Service\Transaction\Output\TrxSaveAsPdf;
use Inventory\Application\Service\Transaction\Output\Pdf\TrxPdfBuilder;
use Inventory\Application\Service\Transaction\Output\Spreadsheet\TrxExcelBuilder;
use Inventory\Application\Service\Transaction\Output\Spreadsheet\TrxOpenOfficeBuilder;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxService extends AbstractService implements TrxServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Service\Contracts\TrxServiceInterface::getDocHeaderByTokenId()
     */
    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN')
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getHeaderById($id, $token);
    }

    public function getTotalRows($id, $token)
    {
        $key = \sprintf("total_row_%s_%s", $id, $token);

        $resultCache = $this->getCache()->getItem($key);

        if (! $resultCache->isHit()) {

            $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
            $trx = $rep->getLazyRootEntityByTokenId($id, $token);

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
     * @see \Inventory\Application\Service\Contracts\TrxServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericTrx) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new TrxRowFormatter(new RowTextAndNumberFormatter());
                $factory = new DocSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new TrxExcelBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsExcel($builder);
                break;
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new TrxOpenOfficeBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_PDF:
                $builder = new TrxPdfBuilder();
                $formatter = new TrxRowFormatter(new RowNumberFormatter());
                $factory = new TrxSaveAsPdf($builder);
                break;

            default:
                $formatter = new TrxRowFormatter(new RowTextAndNumberFormatter());
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
     * @param int $id
     * @param string $token
     * @param string $outputStrategy
     * @return NULL|\Inventory\Domain\Transaction\GenericTrx|NULL
     */
    public function getLazyDocOutputByTokenId($id, $token, $offset, $limit, $outputStrategy, $locale = 'en_EN')
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getDetailLazyRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericTrx) {
            return null;
        }
        $rootEntity->setRowsOutput(null);

        $formatter = new TrxRowFormatter(new RowTextAndNumberFormatter());
        $factory = new LazyDocSaveAsArray();
        $factory->setLogger($this->getLogger());

        $output = $factory->saveAs($rootEntity, $formatter, $offset, $limit);
        $rootEntity->setRowsOutput($output);

        return $rootEntity;
    }

    public function getLazyDocDetailsByTokenId($id, $token)
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        $trx = $rep->getDetailLazyRootEntityByTokenId($id, $token);

        if ($trx == null) {
            return null;
        }

        $key = \sprintf("total_row_%s_%s", $id, $token);
        $resultCache = $this->getCache()->getItem($key);

        if (! $resultCache->isHit()) {
            $total = $trx->getTotalRows();
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        }

        return $trx;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Service\Contracts\TrxServiceInterface::getRootEntityOfRow()
     */
    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {
        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid();

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity instanceof TrxRow) {
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

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}
}

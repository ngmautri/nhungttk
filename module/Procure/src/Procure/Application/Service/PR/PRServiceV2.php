<?php
namespace Procure\Application\Service\PR;

use Application\Application\Helper\Form\FormHelper;
use Application\Application\Helper\Form\FormHelperConst;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Domain\Util\Collection\Render\DefaultRenderAsArray;
use Application\Domain\Util\Collection\Render\DefaultRenderForEmptyCollection;
use Application\Domain\Util\Pagination\Paginator;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Application\Service\Contracts\PrServiceInterface;
use Procure\Application\Service\PR\Output\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Service\PR\RowCollectionRender\DefaultPrRowRenderAsExcel;
use Procure\Application\Service\PR\RowCollectionRender\DefaultPrRowRenderAsHtmlTable;
use Procure\Application\Service\PR\RowCollectionRender\DefaultPrRowRenderAsParamQuery;
use Procure\Application\Service\Render\Formatter\RowTextAndNumberFormatter;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;

/**
 * PR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRServiceV2 extends AbstractService implements PrServiceInterface
{

    const PR_KEY_CACHE = "pr_%s";

    public function getRowCollectionRender(PRDoc $rootEntity, $filter, $page, $resultPerPage = 10, $renderType = SupportedRenderType::HMTL_TABLE, $locale = 'en_EN')
    {
        if ($rootEntity == null) {
            throw new \InvalidArgumentException("Root Entity is null!");
        }

        if (! $filter instanceof PrRowReportSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        // create Paginator
        $totalResults = $rootEntity->getTotalRows();

        $f = '/procure/pr/add-row?target_id=%s&target_token=%s';
        $add_row_url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken());

        if ($totalResults == null or $totalResults == 0) {
            $render = new DefaultRenderForEmptyCollection("PR has no content!");

            $toolbar1 = FormHelper::createButton("Add new line", "add new line", $add_row_url, 'fa fa-plus');
            $render->setToolbar($toolbar1);
            return $render;
        }

        $fullCollection = $rootEntity->getRowCollection()->filter(function ($entry) use ($filter) {

            if ($filter->getBalance() == 100) {
                return true;
            } else {
                return $entry->getTransactionStatus() == $filter->getBalance();
            }
        });

        $totalResults = $fullCollection->count();
        if ($totalResults == null or $totalResults == 0) {
            return new DefaultRenderForEmptyCollection(sprintf("No line '%s'", $filter->getBalance()));
        }

        $paginator = new Paginator($totalResults, $page, $resultPerPage);

        $f = "/procure/pr/row-content?entity_id=%s&entity_token=%s&balance=%s&renderType=%s";
        $url = sprintf($f, $rootEntity->getId(), $rootEntity->getToken(), $filter->getBalance(), $renderType);
        $paginator->setBaseUrl($url);
        $paginator->setUrlConnectorSymbol("&");

        $resultDiv = 'pr_row_div';
        $paginator->setDisplayHTMLDiv($resultDiv);

        // create collection
        $filter->setOffset($paginator->getOffset());
        $filter->setLimit($paginator->getLimit());

        if ($renderType == SupportedRenderType::EXCEL || $renderType == SupportedRenderType::OPEN_OFFICE) {
            $filter->setOffset(null);
            $filter->setLimit(null);
        }

        $filter->setSortBy('createdDate');
        $filter->setSort('DESC');

        $collection = new ArrayCollection($fullCollection->slice($filter->getOffset(), $filter->getLimit()));

        // $format = "/inventory/item-serial/list1?itemId=%s&render_type=%s&page=%s&resultPerPage=%s";
        $format = "/procure/pr/row-content?entity_id=%s&entity_token=%s&renderType=%s&page=%s&resultPerPage=%s";

        $excel_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::EXCEL, $page, $resultPerPage);
        $oo_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::OPEN_OFFICE, $page, $resultPerPage);
        $table_html_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::HMTL_TABLE, $page, $resultPerPage);
        $param_query_url = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::PARAM_QUERY, $page, $resultPerPage);

        $param_onclick = \sprintf("doPaginatorV1('%s','%s')", $table_html_url, $resultDiv);
        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, $resultDiv);

        $list = [
            FormHelper::createLink($excel_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Excel (*.xlxs)'),
            FormHelperConst::DIVIDER,
            FormHelper::createLink($oo_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Open Office (*.ods)')
        ];

        $render = null;
        $toolbar1 = '';

        // $formatter = new PrRowFormatter(new RowTextAndNumberFormatter());
        $formatter = new RowTextAndNumberFormatter();
        $formatter->setLocale($locale);

        switch ($renderType) {

            case SupportedRenderType::HMTL_TABLE:
                $render = new DefaultPrRowRenderAsHtmlTable($totalResults, $collection, $formatter);
                $render->setPaginator($paginator);
                $render->setOffset($paginator->getOffset());
                $render->setUrl(sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), SupportedRenderType::HMTL_TABLE, $page - 1, $resultPerPage));

                if ($rootEntity->getDocStatus() == ProcureDocStatus::DRAFT) {
                    $toolbar1 = $toolbar1 . FormHelper::createButton("Add New Row", "add new PR row", $add_row_url, 'fa fa-plus');
                }

                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-th" aria-hidden="true"></i>', $html_onclick, 'Gird View');

                break;

            case SupportedRenderType::PARAM_QUERY:

                $format = '/procure/pr/row-gird?entity_id=%s&entity_token=%s&pq_rpp=%s&balance=%s';
                $remoteUrl = sprintf($format, $rootEntity->getId(), $rootEntity->getToken(), $resultPerPage, $filter->getBalance());

                $render = new DefaultPrRowRenderAsParamQuery($totalResults, $collection, $formatter);
                $render->setRemoteUrl($remoteUrl);
                $render->setPaginator($paginator);
                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-list" aria-hidden="true"></i>', $param_onclick, 'Table View');

                break;

            case SupportedRenderType::EXCEL:

                $render = new DefaultPrRowRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new ExcelBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::OPEN_OFFICE:

                $render = new DefaultPrRowRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new OpenOfficeBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::AS_ARRAY:
                $render = new DefaultRenderAsArray($totalResults, $collection, $formatter);
                $render->setPaginator($paginator);
                break;

            default:
                $render = new DefaultPrRowRenderAsHtmlTable($totalResults, $collection, $formatter);
                $render->setPaginator($paginator);
                break;
        }

        $toolbar = FormHelper::createDropDownBtn($list, '', $toolbar1);

        $render->setToolbar($toolbar);
        $render->setLogger($this->getLogger());

        return $render;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByTokenId()
     */
    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {
        $key = \sprintf("pr_%s_%s", $id, $token);

        $cache = $this->getCache();
        $cacheItem = $cache->getItem($key);

        if (! $cacheItem->isHit()) {

            $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            $rootEntity->refreshDoc();
            $cacheItem->set(serialize($rootEntity));
            $cacheItem->expiresAfter(60);
            $cache->save($cacheItem);
        } else {
            $rootEntity = unserialize($cache->getItem($key)->get());
        }

        return $rootEntity;
    }

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
     * @deprecated
     * {@inheritdoc}
     * @see \Procure\Application\Service\Contracts\ProcureServiceInterface::getDocDetailsByIdFromDB()
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

    /**
     *
     * @param object $rootEntity
     * @param string $outputStrategy
     * @return object
     */
    private function _getRootEntity($rootEntity, $locale = 'en_EN')
    {}

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

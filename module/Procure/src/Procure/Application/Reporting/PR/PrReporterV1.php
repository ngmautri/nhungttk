<?php
namespace Procure\Application\Reporting\PR;

use Application\Application\Helper\Form\FormHelper;
use Application\Application\Helper\Form\FormHelperConst;
use Application\Application\Service\Document\Spreadsheet\DefaultExcelBuilder;
use Application\Application\Service\Document\Spreadsheet\DefaultOpenOfficeBuilder;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Domain\Util\Collection\Render\DefaultRenderForEmptyCollection;
use Application\Domain\Util\Pagination\Paginator;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Application\Reporting\PR\CollectionRender\DefaultPrRenderAsArray;
use Procure\Application\Reporting\PR\CollectionRender\DefaultPrRenderAsExcel;
use Procure\Application\Reporting\PR\CollectionRender\DefaultPrRenderAsHtmlTable;
use Procure\Application\Reporting\PR\Export\ExportAsExcel;
use Procure\Application\Reporting\PR\Export\ExportAsOpenOffice;
use Procure\Application\Reporting\PR\Output\SaveAsExcel;
use Procure\Application\Reporting\PR\Output\SaveAsHTML;
use Procure\Application\Reporting\PR\Output\SaveAsOpenOffice;
use Procure\Application\Reporting\PR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Reporting\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Service\Output\RowsSaveAsArray;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\NullRowFormatter;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\RowTextAndNumberFormatter;
use Procure\Application\Service\PR\Output\PrRowFormatter;
use Procure\Application\Service\PR\RowCollectionRender\DefaultPrRowRenderAsParamQuery;
use Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;

/**
 * PR Reporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReporterV1 extends AbstractService
{

    /**
     *
     * @var PrReportRepositoryInterface $reporterRespository;
     */
    protected $reporterRespository;

    private $prGrReportRepository;

    public function getHeaderCollectionRender(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows, $page, $resultPerPage = 10, $renderType = SupportedRenderType::HMTL_TABLE)
    {
        if (! $filterHeader instanceof PrHeaderReportSqlFilter) {
            return null;
        }

        // create Paginator
        $totalResults = $this->getListTotal($filterHeader, $filterRows);

        if ($totalResults == null or $totalResults == 0) {
            $render = new DefaultRenderForEmptyCollection();
            return $render;
        }

        $paginator = new Paginator($totalResults, $page, $resultPerPage);

        $url = "/procure/pr-report/header-status-result?" . $filterHeader->printGetQuery();
        $paginator->setBaseUrl($url);
        $paginator->setUrlConnectorSymbol("&");

        $resultDiv = 'result_div';
        $paginator->setDisplayHTMLDiv($resultDiv);

        // create collection
        $filterHeader->setOffset($paginator->getOffset());
        $filterHeader->setLimit($paginator->getLimit());

        if ($renderType == SupportedRenderType::EXCEL || $renderType == SupportedRenderType::OPEN_OFFICE) {
            $filterHeader->setOffset(null);
            $filterHeader->setLimit(null);
        }

        $collection = $this->getList($filterHeader, $filterRows);

        $format = "/procure/pr-report/header-status-result?" . $filterHeader->printGetQuery();
        $excel_url = sprintf($format, "", "", SupportedRenderType::EXCEL, $page, $resultPerPage);
        $oo_url = sprintf($format, "", "", SupportedRenderType::OPEN_OFFICE, $page, $resultPerPage);
        $table_html_url = sprintf($format, "", "", SupportedRenderType::HMTL_TABLE, $page, $resultPerPage);
        $param_query_url = sprintf($format, "", "", SupportedRenderType::PARAM_QUERY, $page, $resultPerPage);

        $param_onclick = \sprintf("doPaginatorV1('%s','%s')", $table_html_url, $resultDiv);
        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, $resultDiv);

        $list = [
            FormHelper::createLink($excel_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Excel (*.xlxs)'),
            FormHelperConst::DIVIDER,
            FormHelper::createLink($oo_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Open Office (*.ods)')
        ];

        $render = null;
        $toolbar1 = '';
        switch ($renderType) {

            case SupportedRenderType::HMTL_TABLE:
                $render = new DefaultPrRenderAsHtmlTable($totalResults, $collection);
                $render->setPaginator($paginator);
                $render->setUrl(sprintf($format, "", "", SupportedRenderType::HMTL_TABLE, $page - 1, $resultPerPage));
                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-th" aria-hidden="true"></i>', $html_onclick, 'Gird View');

                break;

            case SupportedRenderType::PARAM_QUERY:

                $format = '/procure/pr-report/header-status-gird?id=%s&token=%s&pq_rpp=%s';
                $remoteUrl = sprintf($format, "", "", $resultPerPage);

                $render = new DefaultPrRowRenderAsParamQuery($totalResults, $collection);
                $render->setRemoteUrl($remoteUrl);
                $render->setPaginator($paginator);
                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-list" aria-hidden="true"></i>', $param_onclick, 'Table View');

                break;

            case SupportedRenderType::EXCEL:

                $render = new DefaultPrRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new ExcelBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::OPEN_OFFICE:

                $render = new DefaultPrRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new OpenOfficeBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::AS_ARRAY:
                $render = new DefaultPrRenderAsArray($totalResults, $collection);
                $render->setPaginator($paginator);
                break;

            default:
                $render = new DefaultPrRenderAsHtmlTable($totalResults, $collection);
                $render->setPaginator($paginator);
                break;
        }

        $toolbar = FormHelper::createDropDownBtn($list, '', $toolbar1);

        $render->setToolbar($toolbar);

        return $render;
    }

    /**
     *
     * @param SqlFilterInterface $filterHeader
     * @param SqlFilterInterface $filterRows
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getList(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows)
    {
        if (! $filterHeader instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if (! $filterRows instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        return $this->getReporterRespository()->getList($filterHeader, $filterRows);
    }

    /**
     *
     * @param ProcureAppSqlFilterInterface $filter
     * @param string $file_type
     * @param int $totalRecords
     * @return NULL|array|\Doctrine\Common\Collections\ArrayCollection|NULL|string
     */
    public function getPrGrReport(ProcureAppSqlFilterInterface $filter, $file_type, $totalRecords)
    {

        /**
         *
         * @var ArrayCollection $result
         */
        $result = $this->getPrGrReportRepository()->getList($filter);
        if ($result->isEmpty()) {
            return null;
        }

        $factory = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new DefaultExcelBuilder();
                $factory = new ExportAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new DefaultOpenOfficeBuilder();
                $factory = new ExportAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                return $result->toArray();

            default:
                return $result;
        }

        return $factory->execute($result);
    }

    /**
     *
     * @param SqlFilterInterface $filter
     * @return mixed
     */
    public function getPrGrReportTotal(ProcureAppSqlFilterInterface $filter)
    {
        $key = \sprintf("total_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getPrGrReportRepository()->getListTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }
        return $total;
    }

    public function getListTotal(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows)
    {
        $key = \sprintf("total_list_%s", $filterHeader->__toString());

        $cache = $this->getCache();
        $cacheItem = $cache->getItem($key);

        if (! $cacheItem->isHit()) {
            $total = $this->getReporterRespository()->getListTotal($filterHeader, $filterRows);
            $cacheItem->set($total);
            $this->getCache()->save($cacheItem);
        } else {
            $total = $cache->getItem($key)->get();
        }
        return $total;
    }

    public function getAllRow(SqlFilterInterface $filter, $file_type, $locale = 'en_EN')
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;

            $filter->setLimit($limit);
            $filter->setOffset($offset);
        }

        $results = $this->getReporterRespository()->getAllRow($filter);

        // var_dump($results);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new PrRowFormatter(new RowTextAndNumberFormatter());

                $factory = new RowsSaveAsArray();
                break;

            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE:
                $formatter = new RowNumberFormatter();
                $factory = new SaveAsHTML();
                $factory->setOffset($offset);
                $factory->setLimit($limit);
                break;

            default:
                $formatter = new PrRowFormatter(new RowTextAndNumberFormatter());
                $factory = new RowsSaveAsArray();
                break;
        }

        $formatter->setLocale($locale); // new
        return $factory->saveAs($results, $formatter);
    }

    public function getOfItem(SqlFilterInterface $filter, $file_type)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;

            $filter->setLimit($limit);
            $filter->setOffset($offset);
        }

        $results = $this->getReporterRespository()->getAllRow($filter);

        // var_dump($results);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new RowNumberFormatter();
                $factory = new RowsSaveAsArray();
                break;

            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE:
                $formatter = new RowNumberFormatter();
                $factory = new SaveAsHTML();
                $factory->setOffset($offset);
                $factory->setLimit($limit);
                break;

            default:
                $formatter = new RowNumberFormatter();
                $factory = new RowsSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("total_rows_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getReporterRespository()->getAllRowTotal($filter);
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
     * @param PrReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(PrReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface
     */
    public function getPrGrReportRepository()
    {
        return $this->prGrReportRepository;
    }

    /**
     *
     * @param PrGrReportInterface $prGrReportRepository
     */
    public function setPrGrReportRepository(PrGrReportInterface $prGrReportRepository)
    {
        $this->prGrReportRepository = $prGrReportRepository;
    }
}

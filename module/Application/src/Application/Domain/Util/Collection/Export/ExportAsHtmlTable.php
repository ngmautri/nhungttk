<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Application\Helper\FormHelper;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Application\Domain\Util\Pagination\Paginator;
use Traversable;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsHtmlTable extends AbstractExport
{

    private $totalResults;

    private $page;

    private $resultsPerPage;

    private $baseUrl;

    public function execute(Traversable $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
    {
        if ($collection->isEmpty()) {
            return "Nothing found!";
        }

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        if ($filter == null) {
            $filter = new DefaultFilter();
        }

        foreach ($collection as $element) {
            $element = $formatter->format($element);
        }

        $tmp = sprintf('Record %s to %s of %s found!', $filter->getLimit() + 1, $filter->getLimit() + $collection->count());
        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $tmp);

        $table = $result_msg;
        $table = $table . $this->getTableHeader();
        $table = $table . '<tbody>%s</tbody></table>';

        $paginator = new Paginator($this->getTotalResults(), $this->getPage(), $this->getResultsPerPage());

        $pagination = FormHelper::createPaginator($this->getBaseUrl(), $paginator, "&");
        $table = $table . $pagination;

        return sprintf($table, $this->getTableBody());
    }

    /**
     *
     * @return mixed
     */
    public function getTableHeader()
    {
        return $this->tableHeader;
    }

    /**
     *
     * @param mixed $tableHeader
     */
    public function setTableHeader($tableHeader)
    {
        $this->tableHeader = $tableHeader;
    }

    /**
     *
     * @return mixed
     */
    public function getTableBody()
    {
        return $this->tableBody;
    }

    /**
     *
     * @param mixed $tableBody
     */
    public function setTableBody($tableBody)
    {
        $this->tableBody = $tableBody;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    /**
     *
     * @param mixed $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    /**
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     *
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     *
     * @return mixed
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     *
     * @param mixed $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     *
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}


<?php
namespace Application\Domain\Util\Collection\Render;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRenderAsHtmlTable extends AbstractCollectionRender
{

    private $totalResults;

    private $page;

    private $resultsPerPage;

    private $baseUrl;

    private $tableId = 'mytable26';

    private $tableClass = 'table table-bordered table-hover';

    private $tableStyle = 'color:graytext; padding-top:10pt;font:courier;';

    private $tableHtml = '
<table id="%s" class="%s" style="%s">
	<thead>
		%s
	</thead>
	<tbody>
    %s
    </tbody>
</table>';

    /**
     *
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    protected abstract function createHeaderCell();

    protected abstract function createRowCell($element);

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\CollectionRenderInterface::execute()
     */
    public function execute()
    {

        // create Message
        $tableHead = "<tr>\n";
        $tableHead = $tableHead . "<td>#</td>\n"; // default
        $tableHead = $tableHead . $this->createHeaderCell();
        $tableHead = $tableHead . "</tr>";

        $tableBody = '';
        $n = 0;

        foreach ($this->getCollection() as $element) {

            if ($element == null) {
                continue;
            }

            $n ++;

            $tableBody = $tableBody . "<tr>\n";
            $tableBody = $tableBody . sprintf("<td>%s</td>\n", $n + $this->getOffset()); // default
            $tableBody = $tableBody . $this->createRowCell($element);
            $tableBody = $tableBody . "</tr>";
        }

        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $this->printResultCount());
        return $result_msg . sprintf($this->tableHtml, $this->getTableId(), $this->getTableClass(), $this->getTableStyle(), $tableHead, $tableBody);
    }

    /*
     * |=============================
     * | SETTER
     * | GETTER
     * |=============================
     */
    /**
     *
     * @return string
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     *
     * @param string $tableId
     */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }

    /**
     *
     * @return string
     */
    public function getTableClass()
    {
        return $this->tableClass;
    }

    /**
     *
     * @param string $tableClass
     */
    public function setTableClass($tableClass)
    {
        $this->tableClass = $tableClass;
    }

    /**
     *
     * @return string
     */
    public function getTableStyle()
    {
        return $this->tableStyle;
    }

    /**
     *
     * @param string $tableStyle
     */
    public function setTableStyle($tableStyle)
    {
        $this->tableStyle = $tableStyle;
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


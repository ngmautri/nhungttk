<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Helper\Form\FormHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRenderAsHtmlTableWithSelection extends AbstractCollectionRender
{

    private $totalResults;

    private $page;

    private $resultsPerPage;

    private $baseUrl;

    private $tableId = 'mytable26';

    private $tableClass = 'table table-bordered table-hover';

    private $tableStyle = 'color:graytext; padding-top:10pt;font:courier;';

    private $tableHtml = '
<form action="%s" method="post" enctype="multipart/form-data" id="">

<table id="%s" class="%s" style="%s">
	<thead>
		%s
	</thead>
	<tbody>
    %s
    </tbody>
</table>
</form>';

    /*
     * |=============================
     * |Abstract
     * |
     * |=============================
     */
    protected abstract function createHeader();

    protected abstract function createFooter();

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
            $element = $this->getFormatter()->format($element);
            $n ++;

            $tableBody = $tableBody . "<tr>\n";
            $tableBody = $tableBody . sprintf("<td>%s</td>\n", $n + $this->getOffset()); // default
            $tableBody = $tableBody . $this->createRowCell($element);
            $tableBody = $tableBody . "</tr>";
        }

        $format = "confirmRemove(%s)";
        $onclick = sprintf($format, '');
        $title = 'Delete selected items!';

        $btnLable = sprintf('<i class="fa fa-remove" aria-hidden="true"></i><span style="color:red">%s</span>', ' Delete');
        $deleteBtn = FormHelper::createButtonForJS($btnLable, $onclick, $title);

        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div> ', $this->printResultCount());
        $result_msg = $result_msg . sprintf('<div style="display:none; color:red;" id="deleteBtn">%s</div>', $deleteBtn);

        $result_msg = $result_msg . sprintf($this->tableHtml, $this->getUrl(), $this->getTableId(), $this->getTableClass(), $this->getTableStyle(), $tableHead, $tableBody);

        $result_msg = $result_msg . $this->createFooter();

        return $result_msg;
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


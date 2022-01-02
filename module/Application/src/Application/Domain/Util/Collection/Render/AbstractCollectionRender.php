<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Domain\Util\Collection\Contracts\CollectionRenderInterface;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Filter\NullFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Application\Domain\Util\Pagination\Paginator;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractCollectionRender implements CollectionRenderInterface
{

    protected $logger;

    protected $filter;

    protected $formatter;

    protected $collectionCount;

    protected $collection;

    protected $paginator;

    protected $toolbar;

    protected $url;

    /**
     *
     * @param int $collectionCount
     * @param \Traversable $collection
     * @param ElementFormatterInterface $formater
     * @param FilterInterface $filter
     */
    public function __construct($collectionCount, \Traversable $collection, ElementFormatterInterface $formatter = null, FilterInterface $filter = null)
    {
        Assert::notNull($collection, 'Input collection is empty');

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        if ($filter == null) {
            $filter = new NullFilter();
        }

        $this->collectionCount = $collectionCount;
        $this->collection = $collection;
        $this->formatter = $formatter;
        $this->filter = $filter;
    }

    protected function printResultCount()
    {
        if ($this->getPaginator() == null) {
            return null;
        }

        if ($this->getPaginator()->getTotalResults() <= $this->getPaginator()->getResultsPerPage()) {
            $format = "%s records found!";
            return sprintf($format, $this->getPaginator()->getTotalResults());
        }

        $format = "Records %s to %s of %s found!";
        return sprintf($format, $this->getPaginator()->getOffset() + 1, $this->getPaginator()->getOffset() + $this->getPaginator()->getLimit(), $this->getPaginator()->getTotalResults());
    }

    protected function getOffset()
    {
        if ($this->getPaginator() == null) {
            return 0;
        }

        return $this->getPaginator()->getOffset();
    }

    /*
     * |=============================
     * |Tool bar
     * |
     * |=============================
     */
    public function printToolBar()
    {
        return $this->toolbar;
    }

    public function __toString()
    {
        return get_class($this->getFilter());
    }

    /*
     * |=============================
     * |Render pagination by delegating to paginator
     * |
     * |=============================
     */
    public function printPaginator()
    {
        if ($this->getPaginator() == null) {
            return;
        }
        return $this->getPaginator()->printPaginator();
    }

    public function printAjaxPaginator()
    {
        if ($this->getPaginator() == null) {
            return;
        }
        return $this->getPaginator()->printAjaxPaginator();
    }

    public function printCustomPaginator($baseUrl, $connector_symbol)
    {
        if ($this->getPaginator() == null) {
            return;
        }
        return $this->getPaginator()->printCustomPaginator($baseUrl, $connector_symbol);
    }

    public function printCustomAjaxPaginator($baseUrl, $connector_symbol, $result_div)
    {
        if ($this->getPaginator() == null) {
            return;
        }
        return $this->getPaginator()->printCustomAjaxPaginator($baseUrl, $connector_symbol, $result_div);
    }

    /*
     * |=============================
     * |Logging
     * |
     * |=============================
     */

    /**
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function logInfo($message)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->info($message);
        }
    }

    protected function logAlert($message)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->alert($message);
        }
    }

    protected function logException(Exception $e)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->alert($e->getMessage());
        }
    }

    /*
     * |=============================
     * |getter and setter
     * |
     * |=============================
     */

    /**
     *
     * @return \Application\Domain\Util\Collection\Contracts\FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     *
     * @return \Application\Domain\Util\Collection\Contracts\ElementFormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     *
     * @return int
     */
    public function getCollectionCount()
    {
        return $this->collectionCount;
    }

    /**
     *
     * @return \Traversable
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     *
     * @return mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     *
     * @param mixed $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     *
     * @param mixed $toolbar
     */
    public function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;
    }

    /**
     *
     * @return mixed
     */
    public function getToolbar()
    {
        return $this->toolbar;
    }

    /**
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}

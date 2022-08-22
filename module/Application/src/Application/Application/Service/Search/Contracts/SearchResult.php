<?php
namespace Application\Application\Service\Search\Contracts;

/**
 * Search Result
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SearchResult
{

    protected $query;

    protected $queryString;

    protected $message;

    protected $hits;

    public function echoField($fieldName)
    {
        if (! $this->hasHits()) {
            return;
        }

        foreach ($this->getHits() as $hit) {
            if ($hit->__isset($fieldName)) {
                echo $hit->$fieldName . "\n";
            }
        }
    }

    /**
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     *
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    public function __construct($query, $queryString, $message, $hits)
    {
        $this->query = $query;
        $this->queryString = $queryString;
        $this->message = $message;
        $this->hits = $hits;
    }

    public function hasHits()
    {
        return 0 != count($this->hits);
    }

    public function getTotalHits()
    {
        return count($this->hits);
    }

    /**
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @return mixed
     */
    public function getHits()
    {
        return $this->hits;
    }
}

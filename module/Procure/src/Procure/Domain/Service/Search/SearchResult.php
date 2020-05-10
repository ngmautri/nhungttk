<?php
namespace Procure\Domain\Service\Search;

/**
 * Search Result
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SearchResult
{

    protected $message;

    protected $hits;

    public function hasHits()
    {
        return 0 != count($this->hits);
    }

    public function getTotalHits()
    {
        return count($this->hits);
    }

    public function __construct($message, $hits)
    {
        $this->message = $message;
        $this->hits = $hits;
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

    /**
     *
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @param mixed $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }
}

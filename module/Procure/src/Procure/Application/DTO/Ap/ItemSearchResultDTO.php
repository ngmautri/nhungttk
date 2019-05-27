<?php
namespace Inventory\Application\DTO\Item;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemSearchResultDTO
{
    private $message;
    private $hits;
    
    
    public function hatResults(){
        return 0!=count($this->hits);
    }
    
    public function getMessage()
    {
        return $this->message;
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setHits($hits)
    {
        $this->hits = $hits;
    }

}

<?php
namespace Application\Domain\Util;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimpleCollection
{

    protected $items = array();

    public function add($e)
    {
        if ($this->has($e)) {
            return;
        }
        $this->items[$e] = $e;
    }

    public function addChild($e, $child)
    {
        if ($this->has($e)) {
            $this->items[$e][] = array(
                $child
            );
        } else {
            $this->items[$e][][] = $child;
        }
    }

    /**
     *
     * @param mixed $e
     * @return boolean
     */
    public function has($e)
    {
        return isset($this->items[$e]);
    }

    public function getAll()
    {
        return $this->items;
    }
}


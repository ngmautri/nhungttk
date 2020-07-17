<?php
namespace Application\Domain\Util\Composite\Builder;

use Application\Domain\Util\Composite\Composite;
use Application\Domain\Util\Composite\Leaf;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
abstract class AbstractBuilder
{

    protected $data = array();

    protected $index = array();

    abstract public function initCategory();

    /**
     *
     * @param int $parent_id
     * @param int $level
     * @return \Application\Domain\Util\Composite\Composite|\Application\Domain\Util\Composite\Leaf
     */
    public function createComposite($parent_id, $level)
    {
        if (isset($this->index[$parent_id])) {
            $composite = new Composite();
            $composite->setComponentName($this->data[$parent_id]);

            foreach ($this->index[$parent_id] as $cat_id) {

                // pre-order travesal
                $child = $this->createComposite($cat_id, $level + 1);
                $composite->add($child);
            }
        } else {
            $composite = new Leaf();
            $composite->setComponentName($this->data[$parent_id]);
        }
        return $composite;
    }
}

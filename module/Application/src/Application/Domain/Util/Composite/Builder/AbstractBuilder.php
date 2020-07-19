<?php
namespace Application\Domain\Util\Composite\Builder;

use Application\Domain\Util\Composite\AbstractComponent;
use Application\Domain\Util\Composite\Composite;
use Application\Domain\Util\Composite\GenericComponent;
use Application\Domain\Util\Composite\Leaf;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractBuilder
{

    protected $data = array();

    protected $index = array();

    abstract public function initCategory();

    /**
     *
     * @param int $current_id
     * @param int $level
     * @param boolean $updateParent
     * @throws \RuntimeException
     * @return \Application\Domain\Util\Composite\Composite|\Application\Domain\Util\Composite\AbstractComponent
     */
    public function createComposite($current_id, $level, $updateParent = true)
    {
        if ($this->data == null || $this->index == null) {
            throw new \RuntimeException("Input for category buider not set.");
        }

        $data = $this->data[$current_id];
        if (! $data instanceof GenericComponent) {
            throw new \RuntimeException("GenericComponent not set.");
        }

        if (isset($this->index[$current_id])) {
            // has children

            $composite = new Composite();
            $composite->updateFromGenericComponent($data);

            foreach ($this->index[$current_id] as $child_id) {

                // pre-order travesal
                $child = $this->createComposite($child_id, $level + 1, false);
                $composite->add($child);
            }
        } else {

            // has no children
            $composite = new Leaf();
            $composite->updateFromGenericComponent($data);
        }

        if ($updateParent) {
            $parent_id = $data->getParenId();
            if (! $parent_id == null) {

                $parentData = $this->data[$parent_id];
                if ($parentData instanceof GenericComponent) {
                    $parent = new Composite();
                    $parent->updateFromGenericComponent($parentData);
                    $parent->add($composite);
                    $this->updateParent($parent);
                }
            }
        }

        return $composite;
    }

    /**
     *
     * @param AbstractComponent $currentComponent
     */
    public function updateParent(AbstractComponent $currentComponent)
    {
        $parent_id = $currentComponent->getParenId();
        if ($parent_id == null) {
            return $currentComponent;
        }

        $parentData = $this->data[$parent_id];
        if ($parentData instanceof GenericComponent) {
            $parent = new Composite();
            $parent->updateFromGenericComponent($parentData);
            $parent->add($currentComponent);
            $this->updateParent($parent);
        }

        return $currentComponent;
    }
}

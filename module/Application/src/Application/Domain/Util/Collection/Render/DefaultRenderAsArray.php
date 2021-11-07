<?php
namespace Application\Domain\Util\Collection\Render;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultRenderAsArray extends AbstractCollectionRender
{

    public function execute()
    {
        $output = [];
        foreach ($this->getCollection() as $element) {

            $output[] = $this->getFormatter()->format($element);
        }

        return $output;
    }
}


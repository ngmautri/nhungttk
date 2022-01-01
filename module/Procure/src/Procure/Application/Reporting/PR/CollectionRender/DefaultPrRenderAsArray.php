<?php
namespace Procure\Application\Reporting\PR\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractCollectionRender;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRenderAsArray extends AbstractCollectionRender
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


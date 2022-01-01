<?php
namespace Procure\Application\Reporting\PR\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsParamQuery;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRenderAsParamQuery extends AbstractRenderAsParamQuery
{

    protected function createParamQueryObject()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Render\AbstractCollectionRender::printAjaxPaginator()
     */
    public function printAjaxPaginator()
    {
        return null;
    }
}

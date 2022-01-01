<?php
namespace Procure\Application\Service\PR\RowCollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsParamQuery;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrRowRenderAsParamQuery extends AbstractRenderAsParamQuery
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

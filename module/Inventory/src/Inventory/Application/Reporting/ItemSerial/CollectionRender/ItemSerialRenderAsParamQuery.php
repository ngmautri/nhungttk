<?php
namespace Inventory\Application\Reporting\ItemSerial\CollectionRender;

use Application\Domain\Util\Collection\Render\AbstractRenderAsParamQuery;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialRenderAsParamQuery extends AbstractRenderAsParamQuery
{

    protected function createParamQueryObject()
    {
        $data = new ItemSerialParamQueryHelper($this->getRemoteUrl());
        return $data->getOutPut();
    }

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

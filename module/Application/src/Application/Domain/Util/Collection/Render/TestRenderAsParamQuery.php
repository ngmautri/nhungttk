<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Helper\DefaultParamQueryHelper;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TestRenderAsParamQuery extends AbstractRenderAsParamQuery
{

    protected function createParamQueryObject()
    {
        $data = new DefaultParamQueryHelper();
        $data->setRemoteUrl($remoteUrl);
        return $data->getOutPut();
    }
}

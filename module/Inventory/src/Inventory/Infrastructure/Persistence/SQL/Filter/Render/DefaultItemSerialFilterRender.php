<?php
namespace Inventory\Infrastructure\Persistence\SQL\Filter\Render;

use Application\Infrastructure\Persistence\SQL\Contract\SqlFilterHtmlRenderInterface;
use Application\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemSerialFilterRender implements SqlFilterHtmlRenderInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\SQL\Contract\SqlFilterHtmlRenderInterface::render()
     */
    public function render(SqlFilterInterface $filter)
    {
        if ($filter instanceof ItemSerialSqlFilter) {
            return null;
        }
    }
}

<?php
namespace Application\Infrastructure\Persistence\SQL\Contract;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface SqlFilterHtmlRenderInterface
{

    public function render(SqlFilterInterface $filter);
}

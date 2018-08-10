<?php
namespace Procure\Model\Ap;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class APPostingStrategyAbstract
{
    
    abstract public function doPosting($ap, $row);
}
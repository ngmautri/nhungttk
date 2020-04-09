<?php
namespace Procure\Application\Service\Output;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface SpreadsheetBuilderInterface
{
    
    public function setHeader($params);
    
    public function setFooter($params);
}

<?php
namespace Procure\Application\Service\Output\Header;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface HeaderSaveAsInterface
{

    public function saveMultiplyHeaderAs($headers, AbstractHeaderFormatter $formatter);
}

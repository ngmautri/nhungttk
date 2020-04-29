<?php
namespace Procure\Application\Service\Output\Header;

use Procure\Domain\DocSnapshot;

/**
 * Row Output
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractHeaderFormatter
{

    abstract public function format(DocSnapshot $header);
}

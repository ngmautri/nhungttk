<?php
namespace Procure\Domain\Contracts;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface RequestQuantityConverter
{

    public function convert(GenericRow $row);
}
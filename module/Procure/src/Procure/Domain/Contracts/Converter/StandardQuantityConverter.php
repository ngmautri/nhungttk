<?php
namespace Procure\Domain\Contracts\Converter;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface StandardQuantityConverter
{

    public function convert(GenericRow $row);
}
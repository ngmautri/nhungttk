<?php
namespace Procure\Domain\AccountPayable\Converter;

use Procure\Domain\GenericRow;
use Procure\Domain\Contracts\QuantityConverterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApRowQuantityConverter implements QuantityConverterInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Contracts\QuantityConverterInterface::convert()
     */
    public function convert(GenericRow $row)
    {}
}

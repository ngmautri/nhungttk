<?php
namespace Procure\Application\Service\Output\Formatter;

use Procure\Domain\RowSnapshot;

/**
 * Default Row Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NullRowFormatter extends AbstractRowFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format(RowSnapshot $row)
    {
        return $row;
    }
}

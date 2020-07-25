<?php
namespace Inventory\Application\Export\Item\Formatter;

/**
 * Default Row Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NullFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format($row)
    {
        return $row;
    }
}

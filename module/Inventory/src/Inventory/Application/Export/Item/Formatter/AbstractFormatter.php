<?php
namespace Inventory\Application\Export\Item\Formatter;

abstract class AbstractFormatter
{

    abstract public function format($row);
}

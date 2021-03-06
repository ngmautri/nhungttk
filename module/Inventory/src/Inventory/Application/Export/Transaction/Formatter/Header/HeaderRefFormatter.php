<?php
namespace Inventory\Application\Export\Transaction\Formatter\Header;

use Procure\Domain\DocSnapshot;
use Zend\Escaper\Escaper;

/**
 * Default Header Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeaderRefFormatter extends AbstractHeaderFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\Header\AbstractHeaderFormatter::format()
     */
    public function format(DocSnapshot $header)
    {
        if (! $header instanceof DocSnapshot) {
            return null;
        }

        $escaper = new Escaper();

        return $header;
    }
}

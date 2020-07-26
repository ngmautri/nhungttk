<?php
namespace Inventory\Application\Export\Transaction\Header;

use Inventory\Application\Export\Transaction\Contracts\HeadersSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\Header\AbstractHeaderFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeadersSaveAsArray implements HeadersSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\HeadersSaveAsInterface::saveAs()
     */
    public function saveAs($headers, AbstractHeaderFormatter $formatter)
    {
        if (count($headers) == 0) {
            return null;
        }

        $output = array();
        foreach ($headers as $header) {
            $output[] = $formatter->format($header);
        }
        return $output;
    }
}

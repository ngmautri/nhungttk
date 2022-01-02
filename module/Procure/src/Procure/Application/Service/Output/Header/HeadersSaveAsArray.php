<?php
namespace Procure\Application\Service\Output\Header;

use Procure\Application\Service\Output\Contract\HeadersSaveAsInterface;
use Procure\Application\Service\Output\Formatter\Header\AbstractHeaderFormatter;

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

            if ($header == null) {
                continue;
            }
            $output[] = $formatter->format($header);
        }
        return $output;
    }
}

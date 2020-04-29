<?php
namespace Procure\Application\Service\Output\Header;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeaderSaveAsArray implements HeaderSaveAsInterface
{

    public function saveMultiplyHeaderAs($headers, AbstractHeaderFormatter $formatter)
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

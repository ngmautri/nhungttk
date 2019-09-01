<?php
namespace Procure\Application\Service\PO\Output;

use Zend\Escaper\Escaper;
use Zend\Validator\InArray;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowInArray extends PoRowOutputStrategy
{

    public function createOutput($result)
    {
        if (count($result) == 0)
            return null;

        $output = array();

        foreach ($result as $row) {

            /**@var PORow $row ;*/

            if ($row == null) {
                continue;
            }

            $dto = $row->makeDetailsDTO();

            if ($dto == null) {
                continue;
            }

            $output[] = $dto;
        }

        return $output;
    }
}

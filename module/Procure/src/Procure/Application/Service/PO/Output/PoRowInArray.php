<?php
namespace Procure\Application\Service\PO\Output;

use Zend\Escaper\Escaper;
use Zend\Validator\InArray;
use Procure\Domain\PurchaseOrder\GenericPO;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowInArray extends PoRowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\PO\Output\PoRowOutputStrategy::createOutput()
     */
    public function createOutput($po)
    {
        if ($po == null)
            return null;

        /**
         *
         * @var GenericPO $po ;
         */

        if (count($po->getDocRows()) == 0)
            return null;

        $output = array();

        foreach ($po->getDocRows() as $row) {

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

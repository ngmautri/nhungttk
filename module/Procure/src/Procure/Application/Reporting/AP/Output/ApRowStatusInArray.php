<?php
namespace Procure\Application\Reporting\AP\Output;

use Zend\Escaper\Escaper;
use Procure\Domain\APInvoice\APDocRowDetailsSnapshot;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowStatusInArray extends ApRowStatusOutputStrategy
{

  /**
   * 
   * {@inheritDoc}
   * @see \Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy::createOutput()
   */
    public function createOutput($result)
    {
        if (count($result) == 0) {
            return null;
        }

        $output = array();

        foreach ($result as $a) {
            /**@var APDocRowDetailsSnapshot $a ;*/

            $decimalNo = 0;
            $curency = array(
                "USD",
                "THB",
                "EUR"
            );

            if (in_array($a->docCurrencyISO, $curency)) {
                $decimalNo = 2;
            }

            if ($a->unitPrice!==null) {
                $a->unitPrice = number_format($a->unitPrice, $decimalNo);
            }

            if ($a->exchangeRate!==null) {
                $a->exchangeRate = number_format($a->exchangeRate, 0);
            }
       
            if ($a->netAmount!==null) {
                $a->netAmount = number_format($a->netAmount, $decimalNo);
            }
         
            $escaper = new Escaper();
            $output[] = $a;
        }

        return $output;
    }
}

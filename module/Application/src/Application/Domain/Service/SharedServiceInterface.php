<?php
namespace Application\Domain\Service;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface SharedServiceInterface
{
    public function getCurrencyList();
    public function getCountryList();
    public function getMeasurementUnitList();
}

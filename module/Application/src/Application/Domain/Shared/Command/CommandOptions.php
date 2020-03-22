<?php
namespace Application\Domain\Shared\Command;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface CommandOptions
{

    public function getUserId();

    public function getTriggeredBy();

    public function getTriggeredOn();
}

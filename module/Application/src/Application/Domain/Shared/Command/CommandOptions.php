<?php
namespace Application\Domain\Shared\Command;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CommandOptions
{

    /**
     *
     * @return CompanyVO;
     */
    public function getCompanyVO();

    public function getUserId();

    public function getTriggeredBy();

    public function getTriggeredOn();
}

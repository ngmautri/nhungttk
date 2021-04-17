<?php
namespace Application\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface AccountChartValidationServiceInterface
{

    public function getChartValidators();

    public function getAccountValidators();
}

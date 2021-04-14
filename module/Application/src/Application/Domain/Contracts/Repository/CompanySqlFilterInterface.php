<?php
namespace Application\Domain\Contracts\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanySqlFilterInterface extends SqlFilterInterface
{

    public function getCompanyId();
}

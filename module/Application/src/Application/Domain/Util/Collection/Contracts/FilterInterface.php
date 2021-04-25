<?php
namespace Application\Domain\Util\Collection\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface FilterInterface
{

    public function getLimit();

    public function getOffset();
}


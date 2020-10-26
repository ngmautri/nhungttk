<?php
namespace Application\Infrastructure\Persistence\Contracts;

/**
 * Value Object
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CompositeCrudRepositoryInterface extends CrudRepositoryInterface
{

    public function saveMember($rootObject, $localObject);

    public function saveAll($valueObject);
}

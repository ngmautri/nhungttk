<?php
namespace Application\Infrastructure\Persistence\Contracts;

use Application\Domain\Shared\ValueObject;

/**
 * Value Object
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CompositeCrudRepositoryInterface extends CrudRepositoryInterface
{

    public function saveMember(ValueObject $rootObject, ValueObject $localObject);

    public function saveAll($valueObject);
}

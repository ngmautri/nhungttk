<?php
namespace Application\Infrastructure\Persistence\Contracts;

use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;

/**
 * Value Object
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CrudRepositoryInterface
{

    public function save($snapshot);

    public function update($valueObject);

    public function getByKey($key);

    public function delete($valueObject);

    public function getList(DefaultListSqlFilter $filter);
}

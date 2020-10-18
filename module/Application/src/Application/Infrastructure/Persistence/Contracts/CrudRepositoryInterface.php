<?php
namespace Application\Infrastructure\Persistence\Contracts;

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

    public function delete($valueObject);

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);
}

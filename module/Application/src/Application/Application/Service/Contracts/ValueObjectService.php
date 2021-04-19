<?php
namespace Application\Application\Service\ValueObject;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Application\Service\Contracts\AbstractService;
use Application\Domain\Shared\AbstractValueObject;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;

abstract class ValueObjectService extends AbstractService
{

    /**
     *
     * @var ArrayCollection $valueCollecion;
     */
    protected $valueCollecion;

    /**
     *
     * @var CrudRepositoryInterface
     */
    protected $crudRepository;

    protected $filter;

    /**
     *
     * @return CrudRepositoryInterface;
     */
    abstract protected function getCrudRepository();

    abstract protected function createValueObjectFromArray($data);

    abstract protected function createValueObjectFromSnapshot($snapshot);

    abstract protected function getFilter();

    /**
     *
     * @return ArrayCollection
     */
    public function getValueCollecion(DefaultListSqlFilter $filter)
    {
        Assert::notNull($filter);

        if ($this->valueCollecion != null) {

            /**
             *
             * @var ArrayCollection $collection
             */
            $collection = $this->valueCollecion;
            return $collection->slice($filter->getOffset(), $filter->getLimit());
        }

        Assert::notNull($this->getDoctrineEM());
        $results = $this->getCrudRepository()->getList($filter);
        Assert::notNull($results);
        $this->valueCollecion = $results;
        return $this->valueCollecion;
    }

    public function getTotal()
    {
        return $this->getValueCollecion(new DefaultListSqlFilter())->count();
    }

    /**
     *
     * @param AbstractValueObject $valueObject
     * @return boolean
     */
    public function exits(AbstractValueObject $valueObject)
    {
        $collection = $this->getValueCollecion(new DefaultListSqlFilter());
        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param AbstractValueObject $valueObject
     */
    public function add(AbstractValueObject $valueObject)
    {
        $collection = $this->getValueCollecion(new DefaultListSqlFilter());
        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                throw new \RuntimeException("Value object exits");
            }
        }

        $this->getCrudRepository()->save($valueObject->makeSnapshot());
        $collection->add($valueObject);
        $this->valueCollecion = $collection;
    }

    /**
     *
     * @param array $data
     * @throws \RuntimeException
     */
    public function addFrom($data)
    {
        Assert::isArray($data);

        $collection = $this->getValueCollecion(new DefaultListSqlFilter());
        $valueObject = $this->createValueObjectFromArray($data);

        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                throw new \RuntimeException("Value object exits already");
            }
        }
        // \var_dump($valueObject->makeSnapshot());

        $this->getCrudRepository()->save($valueObject->makeSnapshot());
        $collection->add($valueObject);
        $this->valueCollecion = $collection;
    }

    public function getByKey($key)
    {
        $results = $this->getCrudRepository()->getByKey($key);
        return $results;
    }

    /**
     *
     * @param string $key
     * @param object $snapshot
     */
    public function update($key, $data)
    {
        $collection = $this->getValueCollecion(new DefaultListSqlFilter());
        $valueObject = $this->getCrudRepository()->getByKey($key);
        Assert::notNull($valueObject);
        $snapshot = $valueObject->makeSnapshot();
        $snapshot1 = clone $snapshot;

        $excludedProperties = [];
        $snapshot1 = GenericSnapshotAssembler::updateSnapshotFromArrayExcludeFields($snapshot1, $data, $excludedProperties);
        $changes = $snapshot->compare($snapshot1);
        if ($changes == null) {
            throw new \RuntimeException("Nothing change");
        }

        $valueObject1 = $this->createValueObjectFromSnapshot($snapshot1);

        if ($valueObject1->equals($valueObject)) {
            $this->getCrudRepository()->save($valueObject1->makeSnapshot());
            return;
        }

        foreach ($collection as $e) {
            if ($valueObject1->equals($e)) {
                throw new \RuntimeException("Value object exits");
            }
        }

        $this->getCrudRepository()->save($valueObject1->makeSnapshot());
        $this->valueCollecion = $collection;
    }
}

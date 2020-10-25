<?php
namespace Application\Application\Service\Contracts;

use Application\Domain\Shared\AbstractValueObject;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

abstract class CrudsService extends AbstractService
{

    /**
     *
     * @var ArrayCollection $valueCollecion;
     */
    protected static $valueCollecion;

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

    abstract protected function createValueObjectFrom($data);

    abstract protected function getFilter();

    /**
     *
     * @return ArrayCollection
     */
    public function getValueCollecion()
    {
        if (static::$valueCollecion != null) {
            return static::$valueCollecion;
        }

        Assert::notNull($this->getDoctrineEM());
        $sort_by = null;
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;
        $results = $this->getCrudRepository()->getList($this->getFilter(), $sort_by, $sort, $limit, $offset);
        Assert::notNull($results);
        static::$valueCollecion = $results;
        return static::$valueCollecion;
    }

    /**
     *
     * @param AbstractValueObject $valueObject
     * @return boolean
     */
    public function exits(AbstractValueObject $valueObject)
    {
        $collection = $this->getValueCollecion();
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
        $collection = $this->getValueCollecion();
        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                throw new \RuntimeException("Value object exits");
            }
        }

        $this->getCrudRepository()->save($valueObject->makeSnapshot());
        $collection->add($valueObject);
        static::$valueCollecion = $collection;
    }

    /**
     *
     * @param array $data
     * @throws \RuntimeException
     */
    public function addFrom($data)
    {
        Assert::isArray($data);

        $collection = $this->getValueCollecion();
        $valueObject = $this->createValueObjectFrom($data);

        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                throw new \RuntimeException("Value object exits already");
            }
        }
        // \var_dump($valueObject->makeSnapshot());

        $this->getCrudRepository()->save($valueObject->makeSnapshot());
        $collection->add($valueObject);
        static::$valueCollecion = $collection;
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
    public function update($key, $snapshot)
    {
        $collection = $this->getValueCollecion();
        $valueObject = $this->getCrudRepository()->getByKey($key);
        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                throw new \RuntimeException("Value object exits");
            }
        }

        $this->getCrudRepository()->save($valueObject->makeSnapshot());
        static::$valueCollecion = $collection;
    }
}

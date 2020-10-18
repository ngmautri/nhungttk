<?php
namespace Application\Application\Service\ValueObject;

use Application\Application\Service\Contracts\AbstractService;
use Application\Domain\Shared\AbstractValueObject;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use Application\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

abstract class ValueObjectService extends AbstractService
{

    /**
     *
     * @var ArrayCollection $valueCollecion;
     */
    private static $valueCollecion;

    private static $instanceCounter;

    protected $crudRepository;

    protected $filter;

    /**
     *
     * @param CrudRepositoryInterface $crudRepository
     */
    abstract protected function setCrudRepository();

    /**
     *
     * @param SqlFilterInterface $filter
     */
    abstract protected function setFilter();

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
                return;
            }
        }

        $this->getCrudRepository()->save($valueObject->makeSnapshot());

        $collection->add($valueObject);

        static::$valueCollecion = $collection;
    }

    public function update(AbstractValueObject $valueObject, $snapshot)
    {
        $collection = $this->getUomCollecion();
        foreach ($collection as $e) {
            if ($valueObject->equals($e)) {
                return;
            }
        }

        $this->getCrudRepository()->update($valueObject);

        static::$valueCollecion = $collection;
    }

    public static function getInstanceCounter()
    {
        return static::$instanceCounter;
    }

    /**
     *
     * @return \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface
     */
    public function getCrudRepository()
    {
        return $this->crudRepository;
    }

    /**
     *
     * @return \Application\Infrastructure\Persistence\Contracts\SqlFilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}

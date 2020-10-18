<?php
namespace Application\Application\Service\Uom;

use Application\Application\Service\Contracts\AbstractService;
use Application\Application\Service\Uom\Contracts\UomServiceInterface;
use Application\Infrastructure\Persistence\Doctrine\UomQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Filter\UomSqlFilter;
use Webmozart\Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Infrastructure\Mapper\UomMapper;
use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Domain\Shared\Uom\Uom;
use Application\Infrastructure\Persistence\Doctrine\UomCmdRepositoryImpl;

class UomService extends AbstractService implements UomServiceInterface
{

    /**
     *
     * @var ArrayCollection $uomCollecion;
     */
    private static $uomCollecion;

    private static $instanceCounter;

    public function __construct()
    {
        self::$instanceCounter ++;
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getUomCollecion()
    {
        if (static::$uomCollecion != null) {
            return static::$uomCollecion;
        }

        Assert::notNull($this->getDoctrineEM());

        $rep = new UomQueryRepositoryImpl($this->getDoctrineEM());
        $filter = new UomSqlFilter();

        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;

        $results = $rep->getList($filter, $sort_by, $sort, $limit, $offset);
        Assert::notNull($results);

        $collection = new ArrayCollection();
        foreach ($results as $result) {
            $uomSnapshot = UomMapper::createSnapshot($this->getDoctrineEM(), $result, new UomSnapshot());
            $uom = Uom::createFrom($uomSnapshot);
            $collection->contains($uom);
            $collection->add($uom);
        }

        static::$uomCollecion = $collection;
        return static::$uomCollecion;
    }

    /**
     *
     * @param Uom $uom
     * @return boolean
     */
    public function exits(Uom $uom)
    {
        $collection = $this->getUomCollecion();
        foreach ($collection as $e) {
            if ($uom->equals($e)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param Uom $uom
     */
    public function add(Uom $uom)
    {

        $collection = $this->getUomCollecion();
        foreach ($collection as $e) {
            if ($uom->equals($e)) {
                return;
            }
        }
        $rep = new UomCmdRepositoryImpl($this->getDoctrineEM());
        $rep->store($uom->makeSnapshot());

        $collection->add($uom);

        static::$uomCollecion = $collection;
    }

    public static function getInstanceCounter()
    {
        return UomService::$instanceCounter;
    }
}

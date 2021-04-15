<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\Factory\CompanyFactory;
use Application\Domain\Company\Repository\CompanyQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\SqlFilterInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Helper\CompanyHelper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\CompanyMapper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\DepartmentMapper;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyQueryRepositoryImpl extends AbstractDoctrineRepository implements CompanyQueryRepositoryInterface
{

    public function getPostingPeriod($periodId)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationCompany")->findOneBy($criteria);

        Assert::notNull($entity, "NmtApplicationCompany not found!");
        $snapshot = CompanyMapper::createSnapshot($entity, $this->getDoctrineEM());

        $entityRoot = CompanyFactory::contructFromDB($snapshot);
        Assert::notNull($entityRoot);

        return $entityRoot;
    }

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyQueryRepositoryInterface::getDepartmentList()
     */
    public function getDepartmentList(SqlFilterInterface $filter)
    {
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;
        $results = CompanyHelper::getDepartmentList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }
        $tmp = [];
        foreach ($results as $r) {
            $tmp[] = DepartmentMapper::createSnapshot($r);
        }
        return $tmp;
    }
}

<?php
namespace Application\Infrastructure\Doctrine;

use Application\Domain\Company\CompanyQueryRepositoryInterface;
use Application\Domain\Company\Factory\CompanyFactory;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\CompanyMapper;
use Webmozart\Assert\Assert;

/**
 *
 * @deprecated
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

        Assert::notNull($entity);
        $snapshot = CompanyMapper::createSnapshot($entity, $this->getDoctrineEM());
        $entityRoot = CompanyFactory::contructFromDB($snapshot);

        Assert::notNull($entityRoot);
        return $entityRoot;
    }

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}

<?php
namespace HR\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Employee\Repository\IndividualCmdRepositoryInterface;
use HR\Infrastructure\Persistence\Domain\Doctrine\Mapper\IndividualMapper;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualCmdRepositoryImpl extends AbstractDoctrineRepository implements IndividualCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\HrIndividual";

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualCmdRepositoryInterface::store()
     */
    public function store(BaseIndividual $rootEntity, $generateSysNumber = True)
    {
        Assert::notNull($rootEntity, "BaseIndividual not retrieved.");
        $rootSnapshot = $rootEntity->makeSnapshot();

        $increaseVersion = false;

        /**
         *
         * @var \Application\Entity\HrIndividual $entity ;
         *
         */
        if ($rootEntity->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootEntity->getId()));
            }

            $increaseVersion = true;
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = IndividualMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootSnapshot->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->sysNumber = $entity->getSysNumber();
        return $rootSnapshot;
    }
}
<?php
namespace Inventory\Application\Command\Association;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Association\Options\CreateOptions;
use Inventory\Application\DTO\Association\AssociationDTO;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\Validator\DefaultValidator;
use Inventory\Domain\Service\AssociationPostingService;
use Inventory\Domain\Validator\Association\AssociationValidatorCollection;
use Inventory\Infrastructure\Doctrine\AssociationItemCmdRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var AssociationDTO $dto ;
         * @var CreateOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof CreateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {

            $dto->createdBy = $options->getUserId();
            $dto->company = $options->getCompanyId();

            $notification = new Notification();
            $snapshot = $dto;

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());

            $cmdRepository = new AssociationItemCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new AssociationPostingService($cmdRepository);

            $validators = new AssociationValidatorCollection();

            $validator = new DefaultValidator($sharedSpecsFactory);
            $validators->add($validator);

            $rootEntity = BaseAssociation::createFrom($snapshot, $options, $validators, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Item association Created!", $rootEntity->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}

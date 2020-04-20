<?php
namespace Procure\Application\Event\Handler\GR;

use Application\Application\Event\AbstractEventHandler;
use Procure\Application\Command\GR\PostCopyFromAPReservalCmd;
use Procure\Application\Command\GR\PostCopyFromAPReservalCmdHandler;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Exception\OperationFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrFromApReversalPosted extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ApReversed::class => 'onReversed'
        ];
    }

    /**
     *
     * @param ApReversed $ev
     * @throws OperationFailedException
     */
    public function onReversed(ApReversed $ev)
    {

        /**
         *
         * @var APDoc $rootEntity ;
         */
        $rootEntity = $ev->getTarget();

        if (! $rootEntity instanceof APDoc) {
            throw new OperationFailedException(\sprintf("Can not retrived AP Documment", " GrFromApReversalPosted"));
        }

        $options = new PostCopyFromAPOptions($rootEntity->getCompany(), $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);
        $dto = new GrDTO();

        $cmdHandler = new PostCopyFromAPReservalCmdHandler();
        $cmd = new PostCopyFromAPReservalCmd($this->getDoctrineEM(), $dto, $options, $cmdHandler);
        $cmd->execute();
    }
}
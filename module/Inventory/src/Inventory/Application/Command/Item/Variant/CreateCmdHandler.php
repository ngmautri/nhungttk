<?php
namespace Inventory\Application\Command\Item\Variant;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
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
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var ItemDTO $dto ;
         * @var CreateItemOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof CreateItemOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {} catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}

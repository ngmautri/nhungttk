<?php
namespace Application\Domain\Shared\Command;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class CompositeCommand implements CommandInterface
{

    protected $commands;

    /**
     *
     * @param CommandInterface $cmd
     */
    public function add(CommandInterface $cmd)
    {
        $this->commands[] = $cmd;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if ($this->commands == null) {
            return;
        }

        foreach ($this->commands as $cmd) {
            /**
             *
             * @var CommandInterface $cmd ;
             */

            $cmd->execute();
        }
    }

    public function load()
    {}

    public function save()
    {}

    public function getId()
    {}

    public function getEstimatedDuration()
    {}

    public function getOptions()
    {}

    public function getStatus()
    {}

    public function getNotification()
    {}
}

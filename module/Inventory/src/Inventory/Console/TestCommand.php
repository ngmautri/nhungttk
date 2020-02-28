<?php
namespace Inventory\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateUserCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:test');
        
        // the short description shown while running "php app/console list"
        $this->etDescription('Creates a new user.');
        
        // the full command description shown when running the command with
        // the "--help" option
        $this->setHelp('This command allows you to create a user...');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
       echo "hello world!";
    }
}
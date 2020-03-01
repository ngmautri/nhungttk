<?php
namespace Application\Domain\Shared\Command;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CommandInterface
{
    public function getId();
    public function execute();    
    public function save();
    public function load();
    public function getStatus();
    public function getNotification();
    public function getEstimatedDuration();
    public function getOptions();
}

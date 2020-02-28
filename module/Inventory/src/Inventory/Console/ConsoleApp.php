<?php
use Symfony\Component\Console\Application;

$application = new Application();

// ... register commands
$application->add(new TestCommand());
$application->run();
<?php
namespace HR\Domain\Contracts;

/**
 * Document Status
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EmployeeStatus

{

    const EMPLOYED = 1;

    const RESIGNED = - 1;

    const TERMINATED = - 2;

    const QUIT = - 3;
}
<?php
namespace HR\Payroll\Decorator;


Interface AbstractFactoryInterface
{
 
    public function createContractedSalaryDecorator();
 
    public function createAttendanceDecorator();
    
    public function createOverTimeDecorator();
    
    public function createLoyaltyBonusDecorator();
    
    public function createFullPaymentDecorator();
    
}
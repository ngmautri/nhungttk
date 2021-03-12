<?php
namespace HR\Payroll;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Calculator\Visitor\AbstractVisitorElement;
use HR\Payroll\Calculator\Visitor\VisitorInterface;
use HR\Payroll\Exception\LogicException;
use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Income\AbstractIncomeComponent;
use HR\Payroll\Exception\InvalidArgumentException;
use HR\Payroll\Income\Decorator\Factory\PITIncomeDecoratorFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Payroll extends AbstractVisitorElement
{

    private $employee;

    private $payrollInput;

    private $incomeList;

    private $calculatedIncomeList;

    /**
     * Immutable Object
     *
     * @param Employee $employee
     * @param ConsolidatedPayrollInput $payrollInput
     * @param array $incomeList
     */
    public function __construct(Employee $employee, ConsolidatedPayrollInput $payrollInput, array $incomeList)
    {
        if (! $employee instanceof Employee) {
            throw new InvalidArgumentException("Invalid argurment! Employee is expected.");
        }

        if (! $payrollInput instanceof ConsolidatedPayrollInput) {
            throw new InvalidArgumentException("Invalid argurment! Payroll Input is expected.");
        }

        if ($incomeList == null) {
            throw new InvalidArgumentException("Invalid argurment! Nothing is to calculate.");
        }

        $this->employee = $employee;
        $this->payrollInput = $payrollInput;
        $this->incomeList = $incomeList;
    }

    /**
     * Calculation
     *
     * @throws LogicException
     */
    public function calculate()
    {
        $incomeList = $this->incomeList;
        $ssoIncomeAmount = 0;
        $pitIncomeAmount = 0;
        $grossAmount = 0;

        /**@var \HR\Payroll\Employee $e; */
        $e = $this->employee;
        // echo sprintf('==============<h4>%s</h4>',$e->getEmployeeName());
        // echo sprintf('<h5>%s</h5>',$e->getStartWorkingDate()->format("d-m-Y"));
        // echo sprintf('Status: %s </br>',$e->getStatus());

        $this->calculatedIncomeList = array();

        foreach ($incomeList as $income) {

            if (! $income instanceof AbstractIncomeComponent) {
                throw new LogicException("Income Component is not given.");
            }

            /**@var \HR\Payroll\Income\AbstractIncomeComponent $income ; */
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($income->getIncomeDecoratorFactory());

            /**@var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator $decoratedIncome ; */
            $decoratedIncome = $n->createIncomeDecorator($income, $this->payrollInput, 2018);

            $this->calculatedIncomeList[] = $decoratedIncome;

            $grossAmount = $grossAmount + $decoratedIncome->getCalculatedAmount();

            if ($decoratedIncome->isSSOPayable()) {
                $ssoIncomeAmount = $ssoIncomeAmount + $decoratedIncome->getAmount();
            }
            if ($decoratedIncome->isPITPayable()) {
                $pitIncomeAmount = $pitIncomeAmount + $decoratedIncome->getCalculatedAmount();
            }

            // echo "\n<br> Income Name: " . $decoratedIncome->getIncomeName() . "\n<br> Description: " . $decoratedIncome->getDescription() . "--\n<br> Amount: " . $decoratedIncome->getAmount() . "--\n<br> Calculated Amount:" . $decoratedIncome->getCalculatedAmount() . "--\n<br>";
        }

        $ssoIncome = new GenericIncomeComponent("SSO Payable", $ssoIncomeAmount, 0, "usd", FALSE, FALSE, FALSE, FALSE);
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\SSOIncomeDecoratorFactory");

        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($ssoIncome, $this->payrollInput, 2018);

        $this->calculatedIncomeList[] = $decoratedIncome;
        // echo "\n<br> Income Name: " . $decoratedIncome->getIncomeName() . "\n<br> Description: " . $decoratedIncome->getDescription() . "--\n<br> Amount: " . $decoratedIncome->getAmount() . "--\n<br> Calculated Amount:" . $decoratedIncome->getCalculatedAmount() . "--\n<br>";

        $ssoAmount = $decoratedIncome->getCalculatedAmount();

        $pitBase = $pitIncomeAmount - 1000000 - $ssoAmount;
        $pit = new GenericIncomeComponent("PIT Payable", $pitBase, 0, "usd", FALSE, FALSE, FALSE, FALSE);

        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory(PITIncomeDecoratorFactory::class);

        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($pit, $this->payrollInput, 2018);
        $this->calculatedIncomeList[] = $decoratedIncome;

        $pitAmount = $decoratedIncome->getCalculatedAmount();

        // echo "\n<br> Income Name: " . $decoratedIncome->getIncomeName() . "\n<br> Description: " . $decoratedIncome->getDescription() . "--\n<br> Amount: " . $decoratedIncome->getAmount() . "--\n<br> Calculated Amount:" . $decoratedIncome->getCalculatedAmount() . "--\n<br>";

        // echo "<br> Gross: " . $grossAmount;
        // echo "<br> SSO: " . $ssoAmount;
        // echo "<br> PIT: " . $pitAmount;

        $net = $grossAmount - $ssoAmount - $pitAmount;
        // echo "<br> NET: " . $net . '<br>';

        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Calculator\Visitor\AbstractVisitorElement::accept()
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }

    /**
     *
     * @return \HR\Payroll\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     *
     * @return \HR\Payroll\Input\ConsolidatedPayrollInput
     */
    public function getPayrollInput()
    {
        return $this->payrollInput;
    }

    /**
     *
     * @return array
     */
    public function getIncomeList()
    {
        return $this->incomeList;
    }

    /**
     *
     * @return multitype:
     */
    public function getCalculatedIncomeList()
    {
        return $this->calculatedIncomeList;
    }
}

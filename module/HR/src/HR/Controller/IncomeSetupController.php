<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use HR\Payroll\Income\Factory\AbstractIncomeFactoryRegistry;
use Zend\Math\Rand;
/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IncomeSetupController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $incomes = AbstractIncomeFactoryRegistry::getSupportedFactory();
        return new ViewModel(array(
            'incomes' => $incomes
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $incomes = AbstractIncomeFactoryRegistry::getSupportedFactory();
        
        
        
        
        
        if (count($incomes > 0)) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $n=0;
            
            foreach ($incomes as $incomeComponent) {
                /**@var \HR\Payroll\Income\IncomeInterface $incomeComponent ; */
                
                $criteria = array(
                    'decoratorFactory' => $incomeComponent->getIncomeDecoratorFactory(),
                    'salaryName' => $incomeComponent->getIncomeName()
                    // 'className' => $class_name
                );
                $ck = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalaryDefault')->findOneBy($criteria);
                if (! $ck instanceof \Application\Entity\NmtHrSalaryDefault) {
                    $n++;
                    $entity = new \Application\Entity\NmtHrSalaryDefault();
                    $entity->setSalaryName($incomeComponent->getIncomeName());
                    $entity->setDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
                    
                    $entity->setIsPayable($incomeComponent->isPayable());
                    $entity->setIsSsoPayable($incomeComponent->isSSOPayable());
                    $entity->setIsPitPayable($incomeComponent->isPITPayable());
                    $entity->setPaymentFrequency($incomeComponent->getPaymentFrequency());
                    $entity->setDescription($incomeComponent->getDescription());
                    
                    $entity->setIsActive(1);
                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn(new \DateTime());
                    $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                    $this->doctrineEM->persist($entity);
                    
                }
            }
            
            $this->doctrineEM->flush();
        }
        
        return new ViewModel(array(
            'n' => $n
        ));
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}

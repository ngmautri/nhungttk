<?php
namespace Application\Controller\Contracts;

use Application\Application\Service\Contracts\FormOptionCollectionInterface;
use Application\Domain\Company\Factory\CompanyFactory;
use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\CompanyMapper;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractGenericController extends AbstractActionController
{

    protected $doctrineEM;

    protected $logger;

    protected $eventBusService;

    protected $cache;

    protected $company;

    protected $formOptionCollection;
    
    
    protected function modifyPath($path)
    {
        if($path==null){
            return null;
        }
       
        return str_replace('\\', '/', $path);
        
    }

    protected function getGETparam($name, $default = null)
    {
        if (isset($_GET["$name"])) {
            return $_GET["$name"];
        }

        return $default;
    }

    protected function getPOSTparam($name, $default = null)
    {
        if (isset($_POST["$name"])) {
            return $_POST["$name"];
        }

        return $default;
    }

    protected function getLocale()
    {
        $session = new Container('locale');
        if ($session->offsetExists('locale')) {
            return $session->offsetGet('locale');
        }

        return 'en_EN';
    }

    protected function getSharedCollection()
    {
        return $this->sharedCollection();
    }

    /**
     *
     * @return \Zend\Http\Response|\Application\Entity\MlaUsers
     */
    protected function getUser()
    {
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        if ($u == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        return $u;
    }

    protected function getUserId()
    {
        $u = $this->getUser();
        return $u->getId();
    }

    protected function getCompany()
    {
        $u = $this->getUser();
        return $u->getCompany();
    }

    protected function getCompanyVO()
    {
        $entity = $this->getCompany();
        $snapshot = CompanyMapper::createSnapshot($entity);

        if ($snapshot == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $c = CompanyFactory::contructFromDB($snapshot);

        if ($c == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        return $c->createValueObject();
    }

    protected function getCompanyId()
    {
        $c = $this->getCompany();
        if ($c == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        return $c->getId();
    }

    protected function getDefautWarehouseId()
    {
        $c = $this->getCompany();
        if ($c == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $wh = $c->getDefaultWarehouse();
        if ($wh == null) {
            return null;
        }

        return $wh->getId();
    }

    protected function logInfo($m)
    {
        if ($this->getLogger() == null) {
            return;
        }

        $this->getLogger()->info($m);
    }

    protected function logException(Exception $e, $trace = true)
    {
        if ($this->getLogger() == null) {
            return;
        }

        if ($trace) {
            $this->getLogger()->alert($e->getTraceAsString());
        } else {
            $this->getLogger()->alert($e->getMessage());
        }
    }

    protected function getLocalCurrencyId()
    {
        $c = $this->getCompany();
        if ($c == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $curr = $c->getDefaultCurrency();
        if ($curr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        return $curr->getId();
    }

    /**
     *
     * @return \Symfony\Component\Cache\Adapter\AbstractAdapter
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     *
     * @param AbstractAdapter $cache
     */
    public function setCache(AbstractAdapter $cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return \Application\Domain\EventBus\EventBusServiceInterface
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }

    /**
     *
     * @param EventBusServiceInterface $eventBusService
     */
    public function setEventBusService(EventBusServiceInterface $eventBusService)
    {
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return \Application\Application\Service\Contracts\FormOptionCollectionInterface
     */
    public function getFormOptionCollection()
    {
        return $this->formOptionCollection;
    }

    /**
     *
     * @param FormOptionCollectionInterface $formOptionCollection
     */
    public function setFormOptionCollection(FormOptionCollectionInterface $formOptionCollection)
    {
        $this->formOptionCollection = $formOptionCollection;
    }

    /**
     *
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}

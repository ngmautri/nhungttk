<?php
namespace Application\Service;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use Doctrine\ORM\EntityManager;

/*
 * @author nmt
 *
 */
class ApplicationService
{

    protected $moduleManager;

    protected $controllerManager;

    protected $own_modules;

    protected $appResources;

    protected $doctrineEM;

    /**
     */
    public function getLoadedModulesZF3()
    {
        $modules = array();
        $loadedModules = $this->moduleManager->getLoadedModules();
        // $controller = $this->controllerManager->get ( 'User\Controller\AuthController' );

        // var_dump($controller);

        $own_modules = $this->getOwnModules();

        $c = array();
        foreach ($loadedModules as $key => $value) {
            if (in_array($key, $own_modules)) {
                $config = $loadedModules[$key]->getConfig();
                if (count($config) > 0) {
                    // Controller Factory
                    if (count($config['controllers']['factories']) > 0) {
                        $c_factories = $config['controllers']['factories'];
                        if (count($c_factories) > 0) {
                            $con_array = array();

                            foreach ($c_factories as $c_key => $c_value) {
                                $actions = $this->getActions($c_key);
                                $c1 = array(
                                    "Controller" => $c_key,
                                    "Actions" => $actions
                                );
                                $con_array[] = $c1;
                            }

                            $c = array(
                                "Module" => $key,
                                "Controllers" => $con_array
                            );
                        }
                    }
                }
                $modules[] = $c;
            }
        }

        var_dump($modules);
    }

    /**
     *
     * @version 3.0
     * @author Nguyen Mau Tri
     *         Get App Resources
     */
    public function getAppLoadedResources()
    {
        $resources = array();
        $loadedModules = $this->moduleManager->getLoadedModules();
        $own_modules = $this->getOwnModules();
        foreach ($loadedModules as $key => $value) {

            if (in_array($key, $own_modules)) {
                $config = $loadedModules[$key]->getConfig();
                if (count($config) > 0) {

                    // Controller Factory
                    if (count($config['controllers']['factories']) > 0) {
                        $c_factories = $config['controllers']['factories'];
                        if (count($c_factories) > 0) {

                            foreach ($c_factories as $c_key => $c_value) {
                                $data = array();
                                $data = explode("\\", $c_key);
                                $controller_cls = 'NA';
                                if (count($data) == 3) {
                                    $controller_cls = $data[2];
                                }

                                $actions = $this->getActions($c_key);

                                foreach ($actions as $action) {
                                    $res = $c_key . '-' . $action;
                                    $r = array(
                                        "module" => $key,
                                        "controller" => $c_key,
                                        "controller_class" => $controller_cls,
                                        "action" => $action,
                                        "resource" => $res
                                    );
                                    $resources[] = $r;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $resources;
    }

    /**
     *
     * @param string $controller_cls
     * @return string[]
     */
    public function getActions($controller_cls)
    {

        // $controller = $this->controllerManager->get("Application\Controller\Department");
        $controller = $this->controllerManager->get($controller_cls);
        // var_dump($controller);

        $r = new \ReflectionClass($controller);
        $actions = array();

        foreach ($r->getMethods() as $m) {
            $methodName = $m->getName();

            if ($methodName == 'getMethodFromAction') {
                continue;
            }
            if ($methodName !== 'notFoundAction') {
                if (substr($methodName, strlen($methodName) - 6, 6) === 'Action') :
                    $actions[] = substr($methodName, 0, strlen($methodName) - 6);
				endif;

            }
        }

        return $actions;
    }

    /**
     *
     * @deprecated
     */
    public function getLoadedModules()
    {
        $loadedModules = $this->moduleManager->getLoadedModules();
        $modules = array();
        $controllers = $this->getRegisteredControllers();
        $own_modules = array(
            'Application',
            'Workflow',
            'Calendar',
            'User'
        );

        foreach ($loadedModules as $key => $value) {
            // var_dump($value);
            if (in_array($key, $own_modules)) {

                $con_array = array();
                foreach ($controllers as $c) {

                    if ($c[0] === $key) {
                        // get Action
                        $controller_cls = $c[0] . "\\" . $c[1] . "\\" . $c[2];
                        $actions = $this->getActions($controller_cls);
                        $c1 = array(
                            "Controller" => $c[2],
                            "Actions" => $actions
                        );
                        $con_array[] = $c1;
                    }
                }

                $c = array(
                    "Module" => $key,
                    "Controller" => $con_array
                );
            }
            $modules[] = $c;
        }
        return $modules;
    }

    /**
     *
     * @deprecated
     *
     * @return string[]
     */
    public function getResources()
    {
        $registerControllers = $this->controllerManager->getCanonicalNames();

        $resources = array();
        foreach ($registerControllers as $key => $value) {

            if (! preg_match('/(DoctrineORMModule|DoctrineModule)/', $key)) {

                $actions = $this->getActions($key);

                foreach ($actions as $action) {
                    $resources[] = $key . '-' . $action;
                }
            }
            // $resources[] = $c;
        }
        return $resources;
    }

    /**
     *
     * @deprecated
     *
     * @return array[]
     */
    public function getRegisteredControllers()
    {
        $registerControllers = $this->controllerManager->getCanonicalNames();
        // var_dump($registerControllers);

        $this->controllerManager->$data = array();

        foreach ($registerControllers as $key => $value) {
            if (! preg_match('/(DoctrineORMModule|DoctrineModule)/', $key)) {
                $data[] = explode("\\", $key);
            }
        }
        return $data;
    }

    /**
     *
     * @return \Zend\ModuleManager\ModuleManager
     */
    public function getModuleManager()
    {
        return $this->moduleManager;
    }

    /**
     *
     * @param ModuleManager $moduleManager
     * @return \Application\Service\ApplicationService
     */
    public function setModuleManager(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
        return $this;
    }

    /**
     *
     * @return \Zend\Mvc\Controller\ControllerManager
     */
    public function getControllerManager()
    {
        return $this->controllerManager;
    }

    public function setControllerManager(ControllerManager $controllerManager)
    {
        $this->controllerManager = $controllerManager;
        return $this;
    }

    public function getAppResources()
    {
        return $this->appResources;
    }

    public function setAppResources($appResources)
    {
        $this->appResources = $appResources;
        return $this;
    }

    public function getOwnModules()
    {
        $config_file = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config/own_modules.php';
        return include $config_file;
        ;
    }

    public function setOwnModules($own_modules)
    {
        $this->own_modules = $own_modules;
        return $this;
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
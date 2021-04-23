<?php
namespace Application\Application\Contracts;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDTOAssembler
{

    /**
     *
     * @param string $className
     * @return array
     */
    public static function createDTOProperities($className)
    {
        Assert::notNull($className);
        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        return $props;
    }

    /**
     *
     * @param string $className
     * @return object
     */
    public static function getEntity($className)
    {
        Assert::notNull($className);
        $entity = new $className();
        return $entity;
    }

    /**
     *
     * @param string $className
     */
    public static function createStoreMapping($className)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$entity->set%s(\$snapshot->%s);", ucfirst($propertyName), $propertyName);
        }
    }

    /**
     *
     * @param string $className
     */
    public static function createGetMapping($className)
    {
        Assert::notNull($className);

        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$snapshot->%s = \$entity->get%s();", $propertyName, ucfirst($propertyName));
        }
    }

    /**
     *
     * @param string $className
     */
    public static function createFormElements1($className)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $e = '$e= new Element();' . "\n";
            $e = $e . \sprintf('$e->setName("%s");' . "\n", $propertyName);
            $e = $e . \sprintf('$e->setAttributes([\'id\' => \'%s\',\'class\' => \'form-control input-sm\']);' . "\n", $propertyName);
            $e = $e . \sprintf('$e->setOptions([\'label\' => \'%s\']);' . "\n", $propertyName);
            $e = $e . '$this->add($e);' . "\n\n";

            print $e;
        }
    }

    public static function createFormElements($className)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $a = sprintf("[
               'type' => 'text',
               'name' => '%s',
               'attributes' => [
                   'id' => '%s',
                   'class' => 'form-control'
               ],
               'options' => [
                   'label' => '%s'
               ]
           ]", $propertyName, $propertyName, $propertyName);

            $e = \sprintf('$this->add(%s);' . "\n", $a);
            echo $e;
        }
    }

    public static function createFormElementsFor($className, $properties)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (in_array($propertyName, $properties)) {

                echo self::_createFormElement($propertyName);
            }
        }
    }

    public static function createFormElementsExclude($className, $properties)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! in_array($propertyName, $properties)) {

                echo self::_createFormElement($propertyName);
            }
        }
    }

    private static function _createFormElement($propertyName)
    {
        $a = sprintf("[
                   'type' => 'text',
                   'name' => '%s',
                   'attributes' => [
                       'id' => '%s',
                       'class' => \"form-control input-sm\",
                       'required' => FALSE,
                   ],
                   'options' => [
                       'label' => Translator::translate('%s'),
                       'label_attributes' => [
                            'class' => \"control-label col-sm-2\"
                        ]
                   ]
                ]", $propertyName, $propertyName, $propertyName);

        $tmp = "//======================================\n";
        $e = \sprintf("\n%s //Form Element for {%s}\n" . ' %s $this->add(%s);' . "\n", $tmp, $propertyName, $tmp, $a);
        return $e;
    }

    public static function createFormElementsFunctionExclude($className, $properties)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! in_array($propertyName, $properties)) {

                $fName = sprintf('get%s()', ucfirst($propertyName));
                $fContent = sprintf('return $this->get("%s");', $propertyName);

                $f = <<<EOD
public function $fName {
    $fContent\n
}\n
EOD;
                echo $f;
            }
        }
    }

    public static function createFormElementsFunctionFor($className, $properties)
    {
        Assert::notNull($className);

        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);

        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (in_array($propertyName, $properties)) {

                $fName = sprintf('get%s()', ucfirst($propertyName));
                $fContent = sprintf('return $this->get("%s");', $propertyName);

                $f = <<<EOD
public function $fName {
    $fContent\n
}\n
EOD;
                echo $f;
            }
        }
    }
}

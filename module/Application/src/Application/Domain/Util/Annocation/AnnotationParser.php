<?php
namespace Application\Domain\Util\Collection;

use RuntimeException;
use mindplay\annotations\IAnnotationParser;
if (! defined('T_TRAIT')) {
    define(__NAMESPACE__ . '\\T_TRAIT', - 2);
}

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AnnotationParser
{

    const CHAR = - 1;

    const SCAN = 1;

    const CLASS_NAME = 2;

    const SCAN_CLASS = 3;

    const MEMBER = 4;

    const METHOD_NAME = 5;

    const NAMESPACE_NAME = 6;

    const USE_CLAUSE = 11;

    const USE_CLAUSE_AS = 12;

    const TRAIT_USE_CLAUSE = 13;

    const TRAIT_USE_BLOCK = 14;

    const TRAIT_USE_AS = 15;

    const TRAIT_USE_INSTEADOF = 16;

    const SKIP = 7;

    const NAME = 8;

    const COPY_LINE = 9;

    const COPY_ARRAY = 10;

    /**
     *
     * @var boolean $debug Set to TRUE to enable HTML output for debugging
     */
    public $debug = false;

    /**
     *
     * @var boolean Enable PHP autoloader when searching for annotation classes (defaults to true)
     */
    public $autoload = true;

    protected function findAnnotations($str)
    {
        $str = \trim(\preg_replace('/^[\/\*\# \t]+/m', '', $str)) . "\n";
        $str = \str_replace("\r\n", "\n", $str);

        $state = self::SCAN;
        $nesting = 0;
        $name = '';
        $value = '';

        $matches = array();

        for ($i = 0; $i < \strlen($str); $i ++) {
            $char = \substr($str, $i, 1);

            switch ($state) {
                case self::SCAN:
                    if ($char == '@') {
                        $name = '';
                        $value = '';
                        $state = self::NAME;
                    } elseif ($char != "\n" && $char != " " && $char != "\t") {
                        $state = self::SKIP;
                    }
                    break;

                case self::SKIP:
                    if ($char == "\n") {
                        $state = self::SCAN;
                    }
                    break;

                case self::NAME:
                    if (\preg_match('/[a-zA-Z\-\\\\]/', $char)) {
                        $name .= $char;
                    } elseif ($char == ' ') {
                        $state = self::COPY_LINE;
                    } elseif ($char == '(') {
                        $nesting ++;
                        $value = $char;
                        $state = self::COPY_ARRAY;
                    } elseif ($char == "\n") {
                        $matches[] = array(
                            $name,
                            null
                        );
                        $state = self::SCAN;
                    } else {
                        $state = self::SKIP;
                    }
                    break;

                case self::COPY_LINE:
                    if ($char == "\n") {
                        $matches[] = array(
                            $name,
                            $value
                        );
                        $state = self::SCAN;
                    } else {
                        $value .= $char;
                    }
                    break;

                case self::COPY_ARRAY:
                    if ($char == '(') {
                        $nesting ++;
                    }
                    if ($char == ')') {
                        $nesting --;
                    }

                    $value .= $char;

                    if ($nesting == 0) {
                        $matches[] = array(
                            $name,
                            $value
                        );
                        $state = self::SCAN;
                    }
            }
        }

        $annotations = array();

        foreach ($matches as $match) {
            $name = $match[0];
            $type = $this->manager->resolveName($name);

            if ($type === false) {
                continue;
            }

            if (! \class_exists($type, $this->autoload)) {
                // throw new AnnotationException("Annotation type '{$type}' does not exist");
            }

            $value = $match[1];

            $quoted_name = "'#name' => " . \trim(\var_export($name, true));
            $quoted_type = "'#type' => " . \trim(\var_export($type, true));

            if ($value === null) {
                # value-less annotation:
                $annotations[] = "array({$quoted_name}, {$quoted_type})";
            } elseif (\substr($value, 0, 1) == '(') {
                # array-style annotation:
                $annotations[] = "array({$quoted_name}, {$quoted_type}, " . \substr($value, 1);
            } else {
                # PHP-DOC-style annotation:
                if (! \array_key_exists(__NAMESPACE__ . '\IAnnotationParser', \class_implements($type, $this->autoload))) {
                    throw new \RuntimeException("Annotation type '{$type}' does not support PHP-DOC style syntax (because it does not implement the " . __NAMESPACE__ . "\\IAnnotationParser interface)");
                }

                /** @var IAnnotationParser $type */
                $properties = $type::parseAnnotation($value);

                if (! \is_array($properties)) {
                    throw new RuntimeException("Annotation type '{$type}' did not parse correctly");
                }

                $array = "array({$quoted_name}, {$quoted_type}";
                foreach ($properties as $name => $value) {
                    $array .= ", '{$name}' => " . \trim(\var_export($value, true));
                }
                $array .= ")";

                $annotations[] = $array;
            }
        }

        return $annotations;
    }
}

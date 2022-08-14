<?php
namespace Application\Domain\Util;

use Zend\Mvc\I18n\Translator as MvcTranslator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Translator
{

    static public function translate($text)
    {
        return $text;
    }

    /**
     *
     * @param MvcTranslator $translator
     * @param string $text
     * @param string $locale
     * @return \Zend\Mvc\I18n\Translator|string
     */
    static public function execute(MvcTranslator $translator, $text, $locale)

    {
        if ($translator == null) {
            return $text;
        }

        $translator->setLocale($locale);
        return $translator->translate($text);
    }
}


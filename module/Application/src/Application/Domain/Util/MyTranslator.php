<?php
namespace Application\Domain\Util;

use Zend\Mvc\I18n\Translator as MvcTranslator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MyTranslator
{

    private $translator;

    private $locale;

    public function __construct(MvcTranslator $translator = null, $locale = null)
    {
        $this->translator = $translator;
        $this->locale = $locale;
    }

    /**
     *
     * @param MvcTranslator $translator
     * @param string $text
     * @param string $locale
     * @return \Zend\Mvc\I18n\Translator|string
     */
    public function translate($text)

    {
        if ($this->getTranslator() == null) {
            return $text;
        }

        $this->getTranslator()->setLocale($this->getLocale());
        return $this->getTranslator()->translate($text);
    }

    /**
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     *
     * @return \Zend\Mvc\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}


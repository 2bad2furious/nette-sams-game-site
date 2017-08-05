<?php


use Nette\Application\UI\Form;

class BaseForm extends Form {
    const TIMEOUT = "form_timeout";

    public function __construct(Nette\ComponentModel\IContainer $parent = null, $name = null) {
        parent::__construct($parent, $name);
        $this->setTranslator(Translator::instance());
        //$this->addProtection(self::TIMEOUT);
    }
}
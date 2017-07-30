<?php


use Nette\Application\UI\Form;

class TranslatableForm extends Form {
    public function __construct(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
        parent::__construct($parent, $name);
        $this->setTranslator(Translator::instance());
    }
}
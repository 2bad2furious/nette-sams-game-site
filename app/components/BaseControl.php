<?php


use Nette\Application\UI\Presenter;

abstract class BaseControl extends \Nette\Application\UI\Control {
    public function __construct(Presenter $presenter, $name) {
        parent::__construct($presenter, $name);
        $this->initTranslator();
        $this->init();
        $this->template->setFile(__DIR__ . "/templates/" . $this->getName() . ".latte");
    }

    protected abstract function init(): void;


    protected function initTranslator() {
        $this->template->setTranslator(Translator::instance());
    }
}
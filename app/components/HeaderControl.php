<?php


use Nette\Application\UI\Presenter;
use Nette\Security\User;

class HeaderControl extends BaseControl {

    public function render(): void {
        $template = $this->template;
        $template->isLoggedIn = $this->getPresenter()->getUser()->isLoggedIn();
        $template->identity = @$this->getPresenter()->getUser()->getIdentity();
        $template->render();
    }

    protected function init(): void {

    }
}
<?php


use Nette\Application\UI\Presenter;
use Nette\Security\User;

class HeaderControl extends BaseControl {

    public function render(bool $isAlone): void {
        $template = $this->template;
        $template->isLoggedIn = $this->getPresenter()->getUser()->isLoggedIn();
        $template->identity = @$this->getPresenter()->getUser()->getIdentity();
        $template->isAlone = $isAlone;
        $template->isHome = $this->getPresenter()->getName() === "HomePage";
        $template->render();
    }

    protected function init(): void {

    }
}
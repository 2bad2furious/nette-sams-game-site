<?php


class ProfileControl extends BaseControl {

    protected function init(): void {
        // TODO: Implement init() method.
    }

    public function render(): void {
        $template = $this->template;
        $template->identity = $this->getPresenter()->getIdentity();
        $template->render();
    }
}
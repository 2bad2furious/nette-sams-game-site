<?php


class HeaderPresenter extends BasePresenter {

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }

    public function beforeRender() {
        $this->template->isAlone = true;
        parent::beforeRender();
    }
}
<?php


class HomePagePresenter extends BasePresenter {
    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }
}
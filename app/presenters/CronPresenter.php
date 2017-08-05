<?php


class CronPresenter extends BasePresenter {

    public function actionDefault() {

    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }
}
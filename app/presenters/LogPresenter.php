<?php


class LogPresenter extends BasePresenter {

    public function actionDefault() {
        $success = true;
        try {
            \Tracy\Debugger::log($this->getRequest()->getPost("data"));
        } catch (Exception $ex) {
            $success = false;
        }

        $this->payload->success = $success;
        $this->sendPayload();
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }
}
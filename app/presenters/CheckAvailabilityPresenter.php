<?php


class CheckAvailabilityPresenter extends BasePresenter {

    const AVAILABILITY_TYPE_KEYWORD = "type",
        TYPE_EMAIL = "email",
        TYPE_USERNAME = "username",
        AVAILABILITY_TYPES = [self::TYPE_EMAIL, self::TYPE_USERNAME];

    /**
     * status = 1 if ok | 0 if not
     */
    public
    function actionDefault() {
        $status = -1;
        $message = self::NO_PARAMETER_RECEIVED;
        $type = $this->getParameter(self::AVAILABILITY_TYPE_KEYWORD);
        if ($this->isApiMethod() && in_array($type, self::AVAILABILITY_TYPES)) {
            if (isset($_POST["value"]) && $value = $_POST["value"]) {
                if ($type === self::TYPE_USERNAME) {
                    $status = $this->getUserManager()->isUsernameUnique($value, $this->getUser()->getIdentity());
                } else if ($type === self::TYPE_EMAIL) {
                    $status = $this->getUserManager()->isEmailUnique($value, $this->getUser()->getIdentity());
                }

                if ($status === false) {
                    if ($type === self::TYPE_USERNAME) $message = UserManagement::USERNAME_EXISTS;
                    else if ($type === self::TYPE_EMAIL) $message = UserManagement::EMAIL_EXISTS;
                }

            }
            $this->sendJson([
                "status"  => intval($status),
                "message" => $this->getTranslator()->translate($message),
            ]);
        }
    }

    /**
     * for access control
     * @return array
     */
    protected
    function getRoles(): array {
        return UserManager::ROLES;
    }
}
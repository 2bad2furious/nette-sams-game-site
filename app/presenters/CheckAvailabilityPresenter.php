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
        $value = $user = "";
        if ($this->isApiMethod() && in_array($type, self::AVAILABILITY_TYPES)) {
            if (isset($_POST["value"]) && $value = $_POST["value"]) {
                if ($type === self::TYPE_USERNAME) {
                    $user = $this->getUserManager()->getOneByName($value);
                } else if ($type === self::TYPE_EMAIL) {
                    $user = $this->getUserManager()->getOneByEmail($value);
                }

                //sets to 1 if other user found and 0 if not or it is the current user
                $status = intval(!($user instanceof UserIdentity && $user->getId() !== $this->getUser()->getId()));
                if ($status === 0) {
                    if ($type === self::TYPE_USERNAME) $message = UserManagementConstants::USERNAME_EXISTS;
                    else if ($type === self::TYPE_EMAIL) $message = UserManagementConstants::EMAIL_EXISTS;
                } else
                    $message = "";
            }
            $this->sendJson([
                "status"  => $status,
                "message" => Translator::instance()->translate($message),
            ]);
        }
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }
}
<?php

use Nette\Utils\ArrayHash;
use BaseForm as Form;

class ProfilePresenter extends BasePresenter {

    public function renderVerify() {

    }

    /**
     * signs out current user, doesnt care whether hes logged in
     * @return void;
     */
    public function actionSignOut(): void {
        $loggedOut = $this->logOut();
        $message = self::SOMETHING_WENT_WRONG;
        $type = self::MESSAGE_TYPE_ERROR;
        if (is_bool($loggedOut)) {
            if ($loggedOut) {
                $message = UserManagement::SIGN_OUT_SUCCESS;
                $type = self::MESSAGE_TYPE_INFO;
            } else {
                $message = UserManagement::LOGIN_TO_SIGN_OUT;
                $type = self::MESSAGE_TYPE_WARNING;
            }
        }
        $this->flashMessage($message, $type);
        $this->redirect(303, "Auth:logIn");
    }

    public function actionDefault() {
        $this->setIdentity($this->getUser()->getIdentity());
    }

    public function actionEditUsername() {
        $this->setIdentity($this->getUser()->getIdentity());
    }

    public function createComponentEditUsernameForm() {
        function checkUsername(\Nette\Forms\Controls\TextInput $username, UserManager $manager): bool {
            return $manager->isUsernameOk($username->getValue());
        }

        function checkUsernameUniqueness(\Nette\Forms\Controls\TextInput $username, UserManager $manager): bool {
            return $manager->isUsernameUnique($username->getValue());
        }

        $form = new Form();
        $form->addSubmit("edit", UserManagement::PROFILE_EDIT_SUBMIT_LABEL);
        $form->onSuccess[] = [$this, "editUsername"];
        return $form;
    }

    protected function logOut(): ?bool {
        if ($this->getUser()->isLoggedIn()) {
            $this->getUser()->logout(true);
            if ($this->getUser()->isLoggedIn()) {
                return null;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function editUsername(Form $form) {
        $change = $this->getUserManager()->changeUsername($this->getUser()->getId(), $form->getValues()[UserManagement::USERNAME]);
        if ($change) {
            $logout = $this->logOut();
            if ($logout) {
                $this->flashMessage(UserManagement::USER_LOGGED_OUT_TO_CHANGE, self::MESSAGE_TYPE_INFO);
                $this->redirect(303, "User:logIn");
            }
        }
        $this->flashMessage(self::SOMETHING_WENT_WRONG, self::MESSAGE_TYPE_ERROR);
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        if (in_array($this->getAction(), ["verify", "resend"])) {
            return [UserManager::ROLE_USER];
        }
        return UserManager::USERS;
    }

    public function createComponentProfile($name) {
        return new ProfileControl($this, $name);
    }

    public function renderResend() {
        $this->template->message = $this->getTranslator()->translate("");
    }
}
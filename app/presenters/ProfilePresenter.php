<?php

use Nette\Utils\ArrayHash;
use BaseForm as Form;

class ProfilePresenter extends BasePresenter {

    /**
     * signs out current user, doesnt care whether hes logged in
     * @return void;
     */
    public function actionSignOut(): void {
        $loggedOut = $this->logOut();
        $message = self::SOMETHING_WENT_WRONG;
        if (is_bool($loggedOut)) {
            if ($loggedOut)
                $message = UserManagement::SIGN_OUT_SUCCESS;
            else
                $message = UserManagement::LOGIN_TO_SIGN_OUT;
        }
        $this->flashMessage($message);
        $this->redirect(303, "Auth:logIn");
    }

    public
    function actionDefault() {
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
                $this->flashMessage(UserManagement::USER_LOGGED_OUT_TO_CHANGE);
                $this->redirect(303, "User:logIn");
            }
        }
        $this->flashMessage(self::SOMETHING_WENT_WRONG);
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        if ($this->getAction() === "verify") {
            return [UserManager::ROLE_USER];
        }
        return [UserManager::ROLE_USER, UserManager::ROLE_VERIFIED_USER];
    }

    public function actionVerify() {
        diedump($this);
    }


    public function createComponentProfile($name) {
        return new ProfileControl($this, $name);
    }
}
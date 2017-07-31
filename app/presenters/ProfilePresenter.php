<?php

use Nette\Utils\ArrayHash;
use TranslatableForm as Form;

class ProfilePresenter extends BasePresenter {

    /**
     * signs out current user, doesnt care whether hes logged in
     * @return void;
     */
    public function renderSignOut(): void {
        $loggedOut = $this->logOut();
        $message = self::SOMETHING_WENT_WRONG;
        if (is_bool($loggedOut)) {
            if ($loggedOut)
                $message = UserManagementConstants::SIGN_OUT_SUCCESS;
            else
                $message = UserManagementConstants::LOGIN_TO_SIGN_OUT;
        }
        $this->flashMessage($message);
        $this->redirect(303, "User:logIn");
    }

    public
    function renderDefault() {
        $this->setIdentity($this->getUser()->getIdentity());
    }

    public
    function renderEditUsername() {
        $this->setIdentity($this->getUser()->getIdentity());
    }

    public
    function createComponentEditUsernameForm() {
        $form = new Form();
        $form->addText(UserManagementConstants::USERNAME, UserManagementConstants::USERNAME_LABEL)
            ->setRequired(UserManagementConstants::USERNAME_REQUIRED)
            ->addRule(Form::MAX_LENGTH, UserManagementConstants::USERNAME_MAX_LENGTH_TEXT, UserManagementConstants::USERNAME_MAX_LENGTH)
            ->addRule(Form::MIN_LENGTH, UserManagementConstants::USERNAME_MIN_LENGTH_TEXT, UserManagementConstants::USERNAME_MIN_LENGTH)
            ->setDefaultValue($this->getUser()->getIdentity()->getUsername())
            ->addFilter("checkUsername");
        $form->addSubmit("edit", UserManagementConstants::PROFILE_EDIT_SUBMIT_LABEL);
        $form->onValidate[] = [$this, "checkEditUsernameForm"];
        $form->onSuccess[] = [$this, "editUsername"];
        return $form;
    }

    public
    function checkEditUsernameForm(Form $form, ArrayHash $values) {
        $ident = $this->getUserManager()->getOneByName($values[UserManagementConstants::USERNAME]);
        if ($ident instanceof UserIdentity && $ident->getId() !== $this->getUser()->getId()) {
            $form->addError(UserManagementConstants::USERNAME_EXISTS);
        }
    }

    protected
    function logOut(): ?bool {
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

    public
    function editUsername(Form $form) {
        $change = $this->getUserManager()->changeUsername($this->getUser()->getId(), $form->getValues()[UserManagementConstants::USERNAME]);
        if ($change) {
            $logout = $this->logOut();
            if ($logout) {
                $this->flashMessage(UserManagementConstants::USER_LOGGED_OUT_TO_CHANGE);
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
        return [UserManager::ROLE_USER, UserManager::ROLE_VERIFIED_USER];
    }
}
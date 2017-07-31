<?php

use Nette\Security\IAuthenticator;
use Nette\Utils\ArrayHash;
use TranslatableForm as Form;

class AuthPresenter extends BasePresenter {

    /**
     * registers user
     * @param TranslatableForm $form
     */
    public function signUpFormSucceeded(Form $form) {
        $values = $form->getValues(true);
        try {
            $result = $this->getUserManager()->register($values[UserManagementConstants::USERNAME], $values[UserManagementConstants::EMAIL], $values[UserManagementConstants::PASSWORD_1], UserManager::ROLE_USER, false);
        } catch (Exception $ex) {
            $result = false;
        }
        if ($result === true) {
            $this->flashMessage(UserManagementConstants::SIGN_UP_SUCCESS);
            $this->redirect(303, "Auth:LogIn");
        } else {
            $this->flashMessage(self::SOMETHING_WENT_WRONG);
        }
    }

    /**
     * @param TranslatableForm $form
     * @param ArrayHash $values
     */
    public function validateSignUpForm(Form $form, ArrayHash $values) {
        $authenticator = $this->getUserManager();
        $username = $values[UserManagementConstants::USERNAME];
        $password = $values[UserManagementConstants::PASSWORD_1];
        $email = $values[UserManagementConstants::EMAIL];

        if ($authenticator->usernameExists($username)) {
            $form->addError(UserManagementConstants::USERNAME_EXISTS);
        } else if (!$authenticator->isUsernameOk($username))
            $form->addError(UserManagementConstants::USERNAME_ALLOWED_CHARS);

        if ($authenticator->emailExists($email)) {
            $form->addError(UserManagementConstants::EMAIL_EXISTS);
        }

        if (!$authenticator->isPasswordOk($password))
            $form->addError(UserManagementConstants::PASSWORD_MIN_SECURITY);
    }

    /**
     * @return TranslatableForm
     */
    public function createComponentSignUpForm() {
        $form = new Form();
        $form->addText(UserManagementConstants::USERNAME, UserManagementConstants::USERNAME_LABEL)
            ->setRequired(UserManagementConstants::USERNAME_REQUIRED)
            ->addRule(Form::MIN_LENGTH, UserManagementConstants::USERNAME_MIN_LENGTH_TEXT, UserManagementConstants::USERNAME_MIN_LENGTH)
            ->addRule(Form::MAX_LENGTH, UserManagementConstants::USERNAME_MAX_LENGTH_TEXT, UserManagementConstants::USERNAME_MAX_LENGTH);
        $form->addEmail(UserManagementConstants::EMAIL, UserManagementConstants::EMAIL_LABEL)
            ->setRequired(UserManagementConstants::EMAIL_REQUIRED);
        $form->addPassword(UserManagementConstants::PASSWORD_1, UserManagementConstants::PASSWORD_LABEL, null, UserManagementConstants::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagementConstants::PASSWORD_REQUIRED)
            ->addRule([$this->getUserManager(), "isPasswordOk"], UserManagementConstants::PASSWORD_MIN_SECURITY, "")
            ->addRule(Form::MIN_LENGTH, UserManagementConstants::PASSWORD_MIN_LENGTH, 8);
        $form->addPassword(UserManagementConstants::PASSWORD_2, UserManagementConstants::PASSWORD_VERIFY_LABEL, null, UserManagementConstants::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagementConstants::PASSWORD_VERIFY_REQUIRED)
            ->addRule(Form::EQUAL, UserManagementConstants::PASSWORD_MUST_MATCH, $form["password_1"]);
        $form->addSubmit("register", UserManagementConstants::SIGN_UP_SUBMIT_LABEL);
        //checkEmail/UsernameExists And correct values
        $form->onValidate[] = [$this, 'validateSignUpForm'];
        $form->onSubmit[] = [$this, 'signUpFormSucceeded'];
        return $form;
    }

    /**
     * @param TranslatableForm $form
     * @param ArrayHash $values
     */
    public function logInFormSucceeded(Form $form, ArrayHash $values) {
        try {
            $this->getUser()->login($values[UserManagementConstants::USERNAME], $values[UserManagementConstants::PASSWORD]);
            $suc = $this->getUser()->isLoggedIn();
        } catch (Exception $exception) {
            $suc = false;
            $form->addError(self::SOMETHING_WENT_WRONG);
        }
        if ($suc) {
            $this->flashMessage(UserManagementConstants::LOGIN_SUCCESS);
            $this->redirect(303, "Profile:default");
        } else {
            $this->flashMessage(self::SOMETHING_WENT_WRONG);
        }
    }

    /**
     * checks if username exists and if the password is correct
     *
     * @param TranslatableForm $form
     * @param ArrayHash $values
     */
    public function logInFormValidate(Form $form, ArrayHash $values) {
        $authenticator = $this->getUserManager();

        if (!$authenticator->usernameExists($values[UserManagementConstants::USERNAME]))
            $form->addError(UserManagementConstants::USERNAME_NOT_EXIST);
        else {
            try {
                $authenticator->authenticate([
                    IAuthenticator::USERNAME => $values[UserManagementConstants::USERNAME],
                    IAuthenticator::PASSWORD => $values[UserManagementConstants::PASSWORD],
                ]);
            } catch (Exception $ex) {
                if ($ex instanceof \Nette\Security\AuthenticationException)
                    $form->addError(UserManagementConstants::PASSWORD_AUTH_ERROR);
                else
                    $form->addError(self::SOMETHING_WENT_WRONG);
            }
        }
    }

    /**
     * @return TranslatableForm
     */
    public function createComponentLogInForm() {
        $form = new Form();
        $form->addText(UserManagementConstants::USERNAME, UserManagementConstants::USERNAME_LABEL, null, 255)
            ->setRequired(UserManagementConstants::USERNAME_REQUIRED);
        $form->addPassword(UserManagementConstants::PASSWORD, UserManagementConstants::PASSWORD_LABEL, null, 255)
            ->setRequired(UserManagementConstants::PASSWORD_REQUIRED);
        $form->addSubmit("login", UserManagementConstants::LOGIN_SUBMIT_LABEL);
        //checkExistsAndCorrectValues
        $form->onValidate[] = [$this, "logInFormValidate"];
        //login
        $form->onSuccess[] = [$this, "logInFormSucceeded"];
        return $form;
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return [UserManager::ROLE_GUEST];
    }
}
<?php

use Nette\Security\IAuthenticator;
use Nette\Utils\ArrayHash;
use BaseForm as Form;

class AuthPresenter extends BasePresenter {

    /**
     * registers user
     * @param BaseForm $form
     */
    public function signUpFormSucceeded(Form $form) {
        $values = $form->getValues(true);
        try {
            $result = $this->getUserManager()->register($values[UserManagement::USERNAME], $values[UserManagement::EMAIL], $values[UserManagement::PASSWORD_1], UserManager::ROLE_USER, false);
        } catch (Exception $ex) {
            throw $ex;
            $result = false;
        }
        if ($result === true) {
            $this->flashMessage(UserManagement::SIGN_UP_SUCCESS);
            $this->redirect(303, "Auth:LogIn");
        } else {
            $this->flashMessage(self::SOMETHING_WENT_WRONG);
        }
    }

    /**
     * @return BaseForm
     */
    public function createComponentSignUpForm() {
        $form = new Form();
        $form->addText(UserManagement::USERNAME, UserManagement::USERNAME_LABEL)
            ->setRequired(UserManagement::USERNAME_REQUIRED)
            ->addRule(Form::MIN_LENGTH, UserManagement::USERNAME_MIN_LENGTH_TEXT, UserManagement::USERNAME_MIN_LENGTH)
            ->addRule(function (\Nette\Forms\Controls\TextInput $username) {
                return $this->getUserManager()->isUsernameOk($username->getValue());
            }, UserManagement::USERNAME_ALLOWED_CHARS)
            ->addRule(function (\Nette\Forms\Controls\TextInput $username) {
                return $this->getUserManager()->isUsernameUnique($username->getValue());
            }, UserManagement::USERNAME_EXISTS)
            ->addRule(Form::MAX_LENGTH, UserManagement::USERNAME_MAX_LENGTH_TEXT, UserManagement::USERNAME_MAX_LENGTH);
        $form->addEmail(UserManagement::EMAIL, UserManagement::EMAIL_LABEL)
            ->setRequired(UserManagement::EMAIL_REQUIRED)
            ->addRule(function (\Nette\Forms\Controls\TextInput $email) {
                return $this->getUserManager()->isEmailUnique($email->getValue());
            }, UserManagement::EMAIL_EXISTS)
            ->addRule(function (\Nette\Forms\Controls\TextInput $email) {
                return $this->getUserManager()->isEmailOk($email->getValue());
            }, UserManagement::EMAIL_MX_ERROR);
        $form->addPassword(UserManagement::PASSWORD_1, UserManagement::PASSWORD_LABEL, null, UserManagement::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagement::PASSWORD_REQUIRED)
            ->addRule(function (\Nette\Forms\Controls\TextInput $password) {
                return $this->getUserManager()->isPasswordOk($password->getValue());
            }, UserManagement::PASSWORD_MIN_SECURITY)
            ->addRule(Form::MIN_LENGTH, UserManagement::PASSWORD_MIN_LENGTH, UserManagement::PASSWORD_MIN_LENGTH);
        $form->addPassword(UserManagement::PASSWORD_2, UserManagement::PASSWORD_VERIFY_LABEL, null, UserManagement::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagement::PASSWORD_VERIFY_REQUIRED)
            ->addRule(Form::EQUAL, UserManagement::PASSWORD_MUST_MATCH, $form[UserManagement::PASSWORD_1]);
        $form->addSubmit("register", UserManagement::SIGN_UP_SUBMIT_LABEL);
        $form->onSuccess[] = [$this, 'signUpFormSucceeded'];
        return $form;
    }

    /**
     * @param BaseForm $form
     * @param ArrayHash $values
     */
    public function logInFormSucceeded(Form $form, ArrayHash $values) {
        try {
            $this->getUser()->login($values[UserManagement::USERNAME], $values[UserManagement::PASSWORD]);
            $suc = $this->getUser()->isLoggedIn();
        } catch (Exception $exception) {
            $suc = false;
            $form->addError(self::SOMETHING_WENT_WRONG);
        }
        if ($suc) {
            $this->flashMessage(UserManagement::LOGIN_SUCCESS);
            $this->redirect(303, "Profile:default");
        } else {
            $this->flashMessage(self::SOMETHING_WENT_WRONG);
        }
    }

    /**
     * checks if username exists and if the password is correct
     *
     * @param BaseForm $form
     * @param ArrayHash $values
     */
    public function logInFormValidate(Form $form, ArrayHash $values) {
        $authenticator = $this->getUserManager();

        if (!$authenticator->usernameExists($values[UserManagement::USERNAME]))
            $form->addError(UserManagement::USERNAME_NOT_EXIST);
        else {
            try {
                $authenticator->authenticate([
                    IAuthenticator::USERNAME => $values[UserManagement::USERNAME],
                    IAuthenticator::PASSWORD => $values[UserManagement::PASSWORD],
                ]);
            } catch (Exception $ex) {
                if ($ex instanceof \Nette\Security\AuthenticationException)
                    $form->addError(UserManagement::PASSWORD_AUTH_ERROR);
                else
                    $form->addError(self::SOMETHING_WENT_WRONG);
            }
        }
    }

    /**
     * @return BaseForm
     */
    public function createComponentLogInForm() {
        $form = new Form();
        $form->addText(UserManagement::USERNAME, UserManagement::USERNAME_LABEL, null, UserManagement::USERNAME_MAX_LENGTH)
            ->setRequired(UserManagement::USERNAME_REQUIRED);
        $form->addPassword(UserManagement::PASSWORD, UserManagement::PASSWORD_LABEL, null, 255)
            ->setRequired(UserManagement::PASSWORD_REQUIRED);
        $form->addSubmit("login", UserManagement::LOGIN_SUBMIT_LABEL);
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
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
            $identity = $this->getUserManager()->register($values[UserManagement::USERNAME], $values[UserManagement::EMAIL], $values[UserManagement::PASSWORD_1], UserManager::ROLE_USER, false);
        } catch (Exception $ex) {
            $identity = null;
        }
        if ($identity instanceof UserIdentity) {
            //try{
            $mail = $this->getMailer()->sendRegisterationConfirmationAndVerificationEmail($identity);
            //}catch(Exception $ex){
            //$mail = false;
            //}finally{
            if ($mail) {
                $this->flashMessage(UserManagement::SIGN_UP_SUCCESS);
                $this->getUser()->login($identity);
                $this->redirect(303, "Profile:default");
            } else {
                $this->flashMessage(self::SOMETHING_WENT_WRONG, self::MESSAGE_TYPE_ERROR);
            }
            //}
        } else {
            $this->flashMessage(self::SOMETHING_WENT_WRONG, self::MESSAGE_TYPE_ERROR);
        }
    }

    /**
     * @return BaseForm
     */
    public function createComponentSignUpForm() {
        return $this->getFormFactory()->createSignUpForm(
            function (\Nette\Forms\Controls\TextInput $username) {
                return $this->getUserManager()->isUsernameUnique($username->getValue());
            },
            function (\Nette\Forms\Controls\TextInput $email) {
                return $this->getUserManager()->isEmailUnique($email->getValue());
            },
            function (\Nette\Forms\Controls\TextInput $email) {
                return $this->getUserManager()->isEmailOk($email->getValue());
            },
            function (\Nette\Forms\Controls\TextInput $password) {
                return $this->getUserManager()->isPasswordOk($password->getValue());
            },
            [$this, 'signUpFormSucceeded']
        );
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
            $this->flashMessage(self::SOMETHING_WENT_WRONG, self::MESSAGE_TYPE_ERROR);
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
            $form->addError(UserManagement::USERNAME_NOT_EXIST_NEGATIVE);
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
        return $this->getFormFactory()->createLogInForm(
            [$this, "logInFormValidate"],
            [$this, "logInFormSucceeded"]
        );
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return [UserManager::ROLE_GUEST];
    }

}
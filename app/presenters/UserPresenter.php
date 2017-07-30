<?php


use Nette\Security\IAuthenticator;
use Nette\Utils\ArrayHash;
use TranslatableForm as Form;

class UserPresenter extends BasePresenter {
    //name attrs
    const USERNAME = "username",
        PASSWORD = "password",
        PASSWORD_1 = "password_1",
        PASSWORD_2 = "password_2",
        EMAIL = "email";

    const AVAILABILITY_TYPE_KEYWORD = "type",
        TYPE_EMAIL = "email",
        TYPE_USERNAME = "username",
        AVAILABILITY_TYPES = [self::TYPE_EMAIL, self::TYPE_USERNAME];

    //labels
    const USERNAME_LABEL = "username_form_label",
        PASSWORD_LABEL = "password_form_label",
        EMAIL_LABEL = "email_form_label",
        LOGIN_SUBMIT_LABEL = "log_in_submit_form_label",
        SIGN_UP_SUBMIT_LABEL = "register_submit_form_label",
        PROFILE_EDIT_SUBMIT_LABEL = "profile_edit_submit_form_label",
        PASSWORD_VERIFY_LABEL = "password_verify_form_label";

    //errors and messages
    const USERNAME_REQUIRED = "username_required_form_label",
        PASSWORD_REQUIRED = "password_required_form_label",
        USERNAME_NOT_EXIST = "username_not_exist_form_label",
        SIGN_OUT_TO_SIGN_UP = "sign_out_to_sign_up",
        USERNAME_EXISTS = "username_exists_form_label",
        LOGIN_SUCCESS = "login_success",
        SIGN_UP_SUCCESS = "sign_up_success",
        LOGIN_TO_SIGN_OUT = "log_in_to_sign_out",
        USER_ALREADY_LOGGED_IN = "user_already_logged_in",
        PASSWORD_AUTH_ERROR = "password_authentication_error_form_label",
        USERNAME_ALLOWED_CHARS = "username_allowed_form_label",
        EMAIL_EXISTS = "email_exists_form_label",
        PASSWORD_MIN_SECURITY = "password_min_security_form_label",
        SIGN_OUT_SUCCESS = "sign_out_success",
        USERNAME_MIN_LENGTH_TEXT = "username_min_length_form_label",
        USERNAME_MIN_LENGTH = 4,
        EMAIL_REQUIRED = "email_required_form_label",
        PASSWORD_MIN_LENGTH = "password_min_length_form_label",
        PASSWORD_VERIFY_REQUIRED = "password_verify_required_form_label",
        PASSWORD_MUST_MATCH = "password_match_form_label",
        USERNAME_MAX_LENGTH = 40,
        USERNAME_MAX_LENGTH_TEXT = "username_max_length",
        PASSWORD_MAX_LENGTH = 255,
        USER_LOGGED_OUT_TO_CHANGE = "user_logged_out_to_change_stuff";

    private $identity;

    public function renderLogin() {
        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage(self::USER_ALREADY_LOGGED_IN);
            $this->redirect(303, "User:default");
        }
    }

    /**
     * @param TranslatableForm $form
     * @param ArrayHash $values
     */
    public function logInFormSucceeded(Form $form, ArrayHash $values) {
        try {
            $this->getUser()->login($values[self::USERNAME], $values[self::PASSWORD]);
            $suc = $this->getUser()->isLoggedIn();
        } catch (Exception $exception) {
            $suc = false;
            $form->addError(self::SOMETHING_WENT_WRONG);
        }
        if ($suc) {
            $this->flashMessage(self::LOGIN_SUCCESS);
            $this->redirect(301, "User:default");
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

        if (!$authenticator->usernameExists($values[self::USERNAME]))
            $form->addError(self::USERNAME_NOT_EXIST);
        else {
            try {
                $authenticator->authenticate([
                    IAuthenticator::USERNAME => $values[self::USERNAME],
                    IAuthenticator::PASSWORD => $values[self::PASSWORD],
                ]);
            } catch (Exception $ex) {
                if ($ex instanceof \Nette\Security\AuthenticationException)
                    $form->addError(self::PASSWORD_AUTH_ERROR);
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
        $form->addText(self::USERNAME, self::USERNAME_LABEL, null, 255)
            ->setRequired(self::USERNAME_REQUIRED);
        $form->addPassword(self::PASSWORD, self::PASSWORD_LABEL, null, 255)
            ->setRequired(self::PASSWORD_REQUIRED);
        $form->addSubmit("login", self::LOGIN_SUBMIT_LABEL);
        //checkExistsAndCorrectValues
        $form->onValidate[] = [$this, "logInFormValidate"];
        //login
        $form->onSuccess[] = [$this, "logInFormSucceeded"];
        return $form;
    }

    public function renderSignUp() {
        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage(self::SIGN_OUT_TO_SIGN_UP);
            $this->redirect("User:signOut");
        }
    }

    /**
     * registers user
     * @param TranslatableForm $form
     */
    public function signUpFormSucceeded(Form $form) {
        $values = $form->getValues(true);
        try {
            $result = $this->getUserManager()->register($values[self::USERNAME], $values[self::EMAIL], $values[self::PASSWORD_1]);
        } catch (Exception $ex) {
            $result = false;
        }
        if ($result === true) {
            $this->flashMessage(self::SIGN_UP_SUCCESS);
            $this->redirect(303, "User:LogIn");
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
        $username = $values[self::USERNAME];
        $password = $values[self::PASSWORD_1];
        $email = $values[self::EMAIL];

        if ($authenticator->usernameExists($username)) {
            $form->addError(self::USERNAME_EXISTS);
        } else if (!$authenticator->isUsernameOk($username))
            $form->addError(self::USERNAME_ALLOWED_CHARS);

        if ($authenticator->emailExists($email)) {
            $form->addError(self::EMAIL_EXISTS);
        }

        if (!$authenticator->isPasswordOk($password))
            $form->addError(self::PASSWORD_MIN_SECURITY);
    }

    /**
     * @return TranslatableForm
     */
    public function createComponentSignUpForm() {
        $form = new Form();
        $form->addText(self::USERNAME, self::USERNAME_LABEL)
            ->setRequired(self::USERNAME_REQUIRED)
            ->addRule(Form::MIN_LENGTH, self::USERNAME_MIN_LENGTH_TEXT, self::USERNAME_MIN_LENGTH)
            ->addRule(Form::MAX_LENGTH, self::USERNAME_MAX_LENGTH_TEXT, self::USERNAME_MAX_LENGTH);
        $form->addEmail(self::EMAIL, self::EMAIL_LABEL)
            ->setRequired(self::EMAIL_REQUIRED);
        $form->addPassword(self::PASSWORD_1, self::PASSWORD_LABEL, null, self::PASSWORD_MAX_LENGTH)
            ->setRequired(self::PASSWORD_REQUIRED)
            ->addRule([$this->getUserManager(), "isPasswordOk"], self::PASSWORD_MIN_SECURITY, "")
            ->addRule(Form::MIN_LENGTH, self::PASSWORD_MIN_LENGTH, 8);
        $form->addPassword(self::PASSWORD_2, self::PASSWORD_VERIFY_LABEL, null, self::PASSWORD_MAX_LENGTH)
            ->setRequired(self::PASSWORD_VERIFY_REQUIRED)
            ->addRule(Form::EQUAL, self::PASSWORD_MUST_MATCH, $form["password_1"]);
        $form->addSubmit("register", self::SIGN_UP_SUBMIT_LABEL);
        //checkEmail/UsernameExists And correct values
        $form->onValidate[] = [$this, 'validateSignUpForm'];
        $form->onSubmit[] = [$this, 'signUpFormSucceeded'];
        return $form;
    }

    /**
     * signs out current user, doesnt care whether hes logged in
     * @return void;
     */
    public function renderSignOut(): void {
        $loggedOut = $this->logOut();
        $message = self::SOMETHING_WENT_WRONG;
        if (is_bool($loggedOut)) {
            if ($loggedOut)
                $message = self::SIGN_OUT_SUCCESS;
            else
                $message = self::LOGIN_TO_SIGN_OUT;
        }
        $this->flashMessage($message);
        $this->redirect(303, "User:logIn");
    }

    public
    function renderDefault() {
        $this->identity = $this->getUser()->getIdentity();
    }

    public
    function renderAnotherUser() {
        $parameters = $this->getParameters()["parameters"];
        diedump($parameters);
        $this->identity = $this->getUserManager()->getOneByName($parameters);
    }

    public
    function renderEditUsername() {
        $this->identity = $this->getUser()->getIdentity();
    }

    public
    function getIdentity(): UserIdentity {
        $identity = $this->identity;
        if (!$identity instanceof UserIdentity) throw new Exception("Identity needed for current action.");
        return $identity;
    }


    /**
     * status = 1 if ok | 0 if not
     */
    public
    function renderCheckAvailability() {
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
                    if ($type === self::TYPE_USERNAME) $message = self::USERNAME_EXISTS;
                    else if ($type === self::TYPE_EMAIL) $message = self::EMAIL_EXISTS;
                } else
                    $message = "";
            }
            $this->sendJson([
                "status"  => $status,
                "message" => Translator::instance()->translate($message),
            ]);
        }
    }

    public
    function createComponentEditUsernameForm() {
        $form = new Form();
        $form->addText(self::USERNAME, self::USERNAME_LABEL)
            ->setRequired(self::USERNAME_REQUIRED)
            ->addRule(Form::MAX_LENGTH, self::USERNAME_MAX_LENGTH_TEXT, self::USERNAME_MAX_LENGTH)
            ->addRule(Form::MIN_LENGTH, self::USERNAME_MIN_LENGTH_TEXT, self::USERNAME_MIN_LENGTH)
            ->setDefaultValue($this->getUser()->getIdentity()->getUsername())
        ->addFilter("checkUsername");
        $form->addSubmit("edit", self::PROFILE_EDIT_SUBMIT_LABEL);
        $form->onValidate[] = [$this, "checkEditUsernameForm"];
        $form->onSuccess[] = [$this, "editUsername"];
        return $form;
    }

    public
    function checkEditUsernameForm(Form $form, ArrayHash $values) {
        $ident = $this->getUserManager()->getOneByName($values[self::USERNAME]);
        if ($ident instanceof UserIdentity && $ident->getId() !== $this->getUser()->getId()) {
            $form->addError(self::USERNAME_EXISTS);
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
        $change = $this->getUserManager()->changeUsername($this->getUser()->getId(), $form->getValues()[self::USERNAME]);
        if ($change) {
            $logout = $this->logOut();
            if ($logout) {
                $this->flashMessage(self::USER_LOGGED_OUT_TO_CHANGE);
                $this->redirect(303, "User:logIn");
            }
        }
        $this->flashMessage(self::SOMETHING_WENT_WRONG);
    }
}
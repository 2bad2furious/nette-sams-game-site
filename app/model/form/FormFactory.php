<?php

use BaseForm as Form;

class FormFactory {
    private $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    public function createSignUpForm(callable $username_exists_checker, callable $email_exists_checker, callable $email_verifier, callable $password_verifier, callable $onSuccess) {
        $form = new Form($this->translator);

        $form->addText(UserManagement::USERNAME, UserManagement::USERNAME_LABEL)
            ->setRequired(UserManagement::USERNAME_REQUIRED)
            ->addRule(Form::MIN_LENGTH, UserManagement::USERNAME_MIN_LENGTH_TEXT, UserManagement::USERNAME_MIN_LENGTH)
            /*->addRule(function (\Nette\Forms\Controls\TextInput $username) {
                return $this->getUserManager()->isUsernameOk($username->getValue());
            }, UserManagement::USERNAME_ALLOWED_CHARS)*/
            ->addRule(Form::PATTERN, UserManagement::USERNAME_ALLOWED_CHARS, "[0-9a-zA-Z-_+]+")
            ->addRule($username_exists_checker, UserManagement::USERNAME_EXISTS)
            ->addRule(Form::MAX_LENGTH, UserManagement::USERNAME_MAX_LENGTH_TEXT, UserManagement::USERNAME_MAX_LENGTH);

        $form->addEmail(UserManagement::EMAIL, UserManagement::EMAIL_LABEL)
            ->setRequired(UserManagement::EMAIL_REQUIRED)
            ->addRule($email_exists_checker, UserManagement::EMAIL_EXISTS)
            ->addRule($email_verifier, UserManagement::EMAIL_MX_ERROR);

        $form->addPassword(UserManagement::PASSWORD_1, UserManagement::PASSWORD_LABEL, null, UserManagement::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagement::PASSWORD_REQUIRED)
            ->addRule($password_verifier, UserManagement::PASSWORD_MIN_SECURITY)
            ->addRule(Form::MIN_LENGTH, UserManagement::PASSWORD_MIN_LENGTH, UserManagement::PASSWORD_MIN_LENGTH);

        $form->addPassword(UserManagement::PASSWORD_2, UserManagement::PASSWORD_VERIFY_LABEL, null, UserManagement::PASSWORD_MAX_LENGTH)
            ->setRequired(UserManagement::PASSWORD_VERIFY_REQUIRED)
            ->addRule(Form::EQUAL, UserManagement::PASSWORD_MUST_MATCH, $form[UserManagement::PASSWORD_1]);

        $form->addSubmit("register", UserManagement::SIGN_UP_SUBMIT_LABEL);
        $form->onSuccess[] = $onSuccess;
        return $form;
    }

    public function createLogInForm(callable $onValidate, callable $onSuccess): Form {
        $form = new Form($this->translator);

        $form->addText(UserManagement::USERNAME, UserManagement::USERNAME_LABEL, null, UserManagement::USERNAME_MAX_LENGTH)
            ->setRequired(UserManagement::USERNAME_REQUIRED);

        $form->addPassword(UserManagement::PASSWORD, UserManagement::PASSWORD_LABEL, null, 255)
            ->setRequired(UserManagement::PASSWORD_REQUIRED);

        $form->addSubmit("login", UserManagement::LOGIN_SUBMIT_LABEL);
        $form->onValidate[] = $onValidate;
        $form->onSuccess[] = $onSuccess;
        return $form;
    }
}
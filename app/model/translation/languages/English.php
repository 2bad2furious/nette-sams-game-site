<?php


class English extends Language {

    public static function getTable(): array {
        return [
            ""                                    => "",
            "no_parameter_received"               => "No parameter received",
            /* Header */
            "signed_in_as_label"                  => "Signed in as",
            "sign_out_label"                      => "Sign OUT",
            "sign_up_label"                       => "Sign UP",
            "log_in_label"                        => "Log IN",
            /* Forms -start*/
            //nette
            "Please enter a valid email address." => "Please enter a valid email address.",

            //global
            BasePresenter::SOMETHING_WENT_WRONG   => "Something went wrong",

            UserPresenter::PASSWORD_LABEL           => "Password",
            UserPresenter::PASSWORD_MIN_SECURITY    => "Passwords must contain at least 2 uppercase, 2 lowercase letters and 2 numbers",
            UserPresenter::PASSWORD_REQUIRED        => "You must enter your password",
            UserPresenter::PASSWORD_MIN_LENGTH      => "Passwords must have at least %d characters",
            UserPresenter::PASSWORD_VERIFY_LABEL    => "Password again",
            UserPresenter::PASSWORD_VERIFY_REQUIRED => "Enter your password again for verification",
            UserPresenter::PASSWORD_MUST_MATCH      => "Passwords must match",
            UserPresenter::EMAIL_LABEL              => "Email",
            UserPresenter::EMAIL_REQUIRED           => "You must enter your email",
            UserPresenter::EMAIL_EXISTS             => "User with this email already exists",
            UserPresenter::USERNAME_EXISTS          => "User with this username already exists",
            UserPresenter::USERNAME_MAX_LENGTH_TEXT => "Username can be only %d characters long",
            UserPresenter::USERNAME_MIN_LENGTH_TEXT => "Username must be at least %d characters long",


            UserPresenter::USERNAME_REQUIRED         => "You must enter your username",
            UserPresenter::USERNAME_LABEL            => "Username",
            UserPresenter::PASSWORD_MIN_LENGTH       => "Username must consist of at least %d characters",
            UserPresenter::USERNAME_ALLOWED_CHARS    => "Username must contain only alphabet, numbers and -_+ characters",

            //login_form
            UserPresenter::LOGIN_SUBMIT_LABEL        => "Log in",
            UserPresenter::USERNAME_NOT_EXIST        => "User with this username does not exist",
            UserPresenter::PASSWORD_AUTH_ERROR       => "Wrong password, pal",
            UserPresenter::USER_ALREADY_LOGGED_IN    => "You are already logged in",
            UserPresenter::LOGIN_SUCCESS             => "You are now logged in",
            "login_form_title"                       => "Login",

            //register_form
            UserPresenter::SIGN_UP_SUBMIT_LABEL      => "Register",
            UserPresenter::SIGN_UP_SUCCESS           => "Successfully registered",
            "register_form_title"                    => "Register",

            //signout
            UserPresenter::SIGN_OUT_SUCCESS          => "You have been successfully signed out",
            UserPresenter::LOGIN_TO_SIGN_OUT         => "You have to be logged in to sign out",

            //edit_profile
            "edit_your_username"                     => "Edit your username",
            "username_edit_form_title"               => "You can edit your username here",
            UserPresenter::PROFILE_EDIT_SUBMIT_LABEL => "Edit",
            UserPresenter::USER_LOGGED_OUT_TO_CHANGE => "You were signed out for the changes to take place"

            /* Forms -end */
        ];
    }
}
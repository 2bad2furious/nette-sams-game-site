<?php


class English extends Language {

    public static function getTable(): array {
        return [
            ""                                    => "",
            "no_parameter_received"               => "No parameter received",
            "site_title"                          => "XD - THE GAME",
            "site_title_short"                    => "XD",
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

            UserManagementConstants::PASSWORD_LABEL           => "Password",
            UserManagementConstants::PASSWORD_MIN_SECURITY    => "Passwords must contain at least 2 uppercase, 2 lowercase letters and 2 numbers",
            UserManagementConstants::PASSWORD_REQUIRED        => "You must enter your password",
            UserManagementConstants::PASSWORD_MIN_LENGTH      => "Passwords must have at least %d characters",
            UserManagementConstants::PASSWORD_VERIFY_LABEL    => "Password again",
            UserManagementConstants::PASSWORD_VERIFY_REQUIRED => "Enter your password again for verification",
            UserManagementConstants::PASSWORD_MUST_MATCH      => "Passwords must match",
            UserManagementConstants::EMAIL_LABEL              => "Email",
            UserManagementConstants::EMAIL_REQUIRED           => "You must enter your email",
            UserManagementConstants::EMAIL_EXISTS             => "User with this email already exists",
            UserManagementConstants::USERNAME_EXISTS          => "User with this username already exists",
            UserManagementConstants::USERNAME_MAX_LENGTH_TEXT => "Username can be only %d characters long",
            UserManagementConstants::USERNAME_MIN_LENGTH_TEXT => "Username must be at least %d characters long",


            UserManagementConstants::USERNAME_REQUIRED                   => "You must enter your username",
            UserManagementConstants::USERNAME_LABEL                      => "Username",
            UserManagementConstants::PASSWORD_MIN_LENGTH                 => "Username must consist of at least %d characters",
            UserManagementConstants::USERNAME_ALLOWED_CHARS              => "Username must contain only alphabet, numbers and -_+ characters",

            //login_form
            UserManagementConstants::LOGIN_SUBMIT_LABEL                  => "Log in",
            UserManagementConstants::USERNAME_NOT_EXIST                  => "User with this username does not exist",
            UserManagementConstants::PASSWORD_AUTH_ERROR                 => "Wrong password, pal",
            UserManagementConstants::USER_ALREADY_LOGGED_IN              => "You are already logged in",
            UserManagementConstants::LOGIN_SUCCESS                       => "You are now logged in",
            "login_form_title"                                           => "Login",

            //register_form
            UserManagementConstants::SIGN_UP_SUBMIT_LABEL                => "Register",
            UserManagementConstants::SIGN_UP_SUCCESS                     => "Successfully registered",
            "register_form_title"                                        => "Register",

            //signout
            UserManagementConstants::SIGN_OUT_SUCCESS                    => "You have been successfully signed out",
            UserManagementConstants::LOGIN_TO_SIGN_OUT                   => "You have to be logged in to sign out",

            //edit_profile
            "edit_your_username"                                         => "Edit your username",
            "username_edit_form_title"                                   => "You can edit your username here",
            UserManagementConstants::PROFILE_EDIT_SUBMIT_LABEL           => "Edit",
            UserManagementConstants::USER_LOGGED_OUT_TO_CHANGE           => "You were signed out for the changes to take place",

            /* Forms -end */

            //rights
            UserManagementConstants::ACTION_FOR_GUEST_ONLY               => "This action requires you to be logged out",
            UserManagementConstants::ACTION_FOR_GUEST_ONLY_ADDITIONAL    => "You can sign out <a href='%s'>here</a>.",
            UserManagementConstants::ACTION_FOR_VERIFIED_ONLY            => "This action requires you to verify your account",
            UserManagementConstants::ACTION_FOR_VERIFIED_ONLY_ADDITIONAL => "You should have the verification email in your inbox. If not, you can resend it <a href='%s'>here</a>",
            UserManagementConstants::ACTION_FOR_USERS_ONLY               => "This action requires you to be logged in",
            UserManagementConstants::ACTION_FOR_USERS_ONLY_ADDITIONAL     => "You should <a href='%2\$s'>Sign Up</a> and then / or <a href='%1\$s'>Log in</a>",
        ];
    }
}
<?php


class English extends Language {

    public static function getTable(): array {
        return [
            ""                                    => "empty",
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
            "Please enter a valid email address." => "Please enter a valid email address",
            "This field is required." => "This field is required",

            //global
            BasePresenter::SOMETHING_WENT_WRONG   => "Something went wrong",

            UserManagement::PASSWORD_LABEL           => "Password",
            UserManagement::PASSWORD_MIN_SECURITY    => "Passwords must contain at least 2 uppercase, 2 lowercase letters and 2 numbers",
            UserManagement::PASSWORD_REQUIRED        => "You must enter your password",
            UserManagement::PASSWORD_MIN_LENGTH_TEXT => "Passwords must have at least %d characters",
            UserManagement::PASSWORD_VERIFY_LABEL    => "Password again",
            UserManagement::PASSWORD_VERIFY_REQUIRED => "Enter your password again for verification",
            UserManagement::PASSWORD_MUST_MATCH      => "Passwords must match",
            UserManagement::EMAIL_LABEL              => "Email",
            UserManagement::EMAIL_REQUIRED           => "You must enter your email",
            UserManagement::EMAIL_EXISTS             => "User with this email already exists",
            UserManagement::USERNAME_EXISTS          => "User with this username already exists xd",
            UserManagement::USERNAME_MAX_LENGTH_TEXT => "Username can be only %d characters long",
            UserManagement::USERNAME_MIN_LENGTH_TEXT => "Username must be at least %d characters long",


            UserManagement::USERNAME_REQUIRED                   => "You must enter your username",
            UserManagement::USERNAME_LABEL                      => "Username",
            UserManagement::PASSWORD_MIN_LENGTH                 => "Password must consist of at least %d characters",
            UserManagement::USERNAME_ALLOWED_CHARS              => "Username must contain only alphabet, numbers and -_+ characters",

            //login_form
            UserManagement::LOGIN_SUBMIT_LABEL                  => "Log in",
            UserManagement::USERNAME_NOT_EXIST                  => "User with this username does not exist",
            UserManagement::PASSWORD_AUTH_ERROR                 => "Wrong password, pal",
            UserManagement::USER_ALREADY_LOGGED_IN              => "You are already logged in",
            UserManagement::LOGIN_SUCCESS                       => "You are now logged in",
            "login_form_title"                                  => "Login",

            //register_form
            UserManagement::SIGN_UP_SUBMIT_LABEL                => "Register",
            UserManagement::SIGN_UP_SUCCESS                     => "Successfully registered",
            "register_form_title"                               => "Register",
            UserManagement::EMAIL_MX_ERROR                      => "Domain you have provided does not have a valid MX record - Enter a valid email address",

            //signout
            UserManagement::SIGN_OUT_SUCCESS                    => "You have been successfully signed out",
            UserManagement::LOGIN_TO_SIGN_OUT                   => "You have to be logged in to sign out",

            //edit_profile
            "edit_your_username"                                => "Edit your username",
            "username_edit_form_title"                          => "You can edit your username here",
            UserManagement::PROFILE_EDIT_SUBMIT_LABEL           => "Edit",
            UserManagement::USER_LOGGED_OUT_TO_CHANGE           => "You were signed out for the changes to take place",

            /* Forms -end */

            //rights
            UserManagement::ACTION_FOR_GUEST_ONLY               => "This action requires you to be logged out",
            UserManagement::ACTION_FOR_GUEST_ONLY_ADDITIONAL    => "You can sign out <a href='%s'>here</a>.",
            UserManagement::ACTION_FOR_VERIFIED_ONLY            => "This action requires you to verify your account",
            UserManagement::ACTION_FOR_VERIFIED_ONLY_ADDITIONAL => "You should have the verification email in your inbox. If not, you can resend it <a href='%s'>here</a>",
            UserManagement::ACTION_FOR_USERS_ONLY               => "This action requires you to be logged in",
            UserManagement::ACTION_FOR_USERS_ONLY_ADDITIONAL    => "You should <a href='%2\$s'>Sign Up</a> and/or <a href='%1\$s'>Log in</a>",
        ];
    }
}
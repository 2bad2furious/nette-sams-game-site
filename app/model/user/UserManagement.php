<?php

/**
 * This class was created just for consistency of used constants across multiple forms and pages.
*/
abstract class UserManagement {

    //errors and messages
    const USERNAME_REQUIRED = "username_required_form_label",
        PASSWORD_REQUIRED = "password_required_form_label",
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
        PASSWORD_MIN_LENGTH = 8,
        PASSWORD_MIN_LENGTH_TEXT = "password_min_length_form_label",
        PASSWORD_VERIFY_REQUIRED = "password_verify_required_form_label",
        PASSWORD_MUST_MATCH = "password_match_form_label",
        USERNAME_MAX_LENGTH = 40,
        USERNAME_MAX_LENGTH_TEXT = "username_max_length",
        PASSWORD_MAX_LENGTH = 255,
        USER_LOGGED_OUT_TO_CHANGE = "user_logged_out_to_change_stuff",
        ACTION_FOR_GUEST_ONLY = "action_for_guests_only",
        ACTION_FOR_GUEST_ONLY_ADDITIONAL = "action_for_guest_only_additional",
        ACTION_FOR_VERIFIED_ONLY = "action_for_verified_only",
        ACTION_FOR_VERIFIED_ONLY_ADDITIONAL = "action_for_verified_only_additional",
        ACTION_FOR_USERS_ONLY = "action_for_users_only",
        ACTION_FOR_USERS_ONLY_ADDITIONAL = "action_for_users_only_additional",
        EMAIL_MX_ERROR = "email_mx_error",
        EMAIL_NOT_EXIST_POSITIVE = "email_not_exist_positive",
        EMAIL_NOT_EXIST_NEGATIVE = "email_not_exist_negative",
        USERNAME_NOT_EXIST_POSITIVE = "username_not_exist_positive",
        USERNAME_NOT_EXIST_NEGATIVE = "username_not_exist_negative";

    //name attrs
    const USERNAME = "username",
        PASSWORD = "password",
        PASSWORD_1 = "password_1",
        PASSWORD_2 = "password_2",
        EMAIL = "email";

    //labels
    const USERNAME_LABEL = "username_form_label",
        PASSWORD_LABEL = "password_form_label",
        EMAIL_LABEL = "email_form_label",
        LOGIN_SUBMIT_LABEL = "log_in_submit_form_label",
        SIGN_UP_SUBMIT_LABEL = "register_submit_form_label",
        PROFILE_EDIT_SUBMIT_LABEL = "profile_edit_submit_form_label",
        PASSWORD_VERIFY_LABEL = "password_verify_form_label";

}
/**
 * Created by user on 26.07.2017.
 */
checkLinksForBeingActive = function () {
    curLink = window.location.pathname;
    $("a").each(function (i) {
        iter = $(this)
        link = iter.attr("href");
        if (curLink === link) iter.addClass("disabled");
        if (curLink.split(link).length === 2) iter.addClass("active");
    })
}

checkAvailability = function (type, val) {
    if (typeof type !== "string" || typeof val !== "string")
        throw new Error("wrong parameters passed");
    if (type !== "email" && type !== "username")
        throw new Error(type + " is probably not supported");

    //TODO check upon submiting

    $.nette.ajax({
        url: "/api/user/check-availability/" + type + "/",
        data: {"value": val},
        method: "POST",
        success: function (r) {

        }
    })
}

bindUsernameChecker = function () {
    username = $("input[name=username]");
    console.info(username.length + " username")
    if (username.length && username.attr("id").split("logInForm").length === 1) {
        username.focusout(function () {
            console.log(username.val() + " u val")
            checkAvailability("username", username.val())
        })
    }
}

bindEmailChecker = function () {
    email = $("input[name=email]");
    console.info(email.length + " email")
    if (email.length) {
        email.focusout(function () {
            console.log(email.val() + " e val");
            checkAvailability("email", email.val())
        })
    }
}

toggleMenu = function () {
    obj = $("div#header-container");
    if (obj.length) {
        if (obj.hasClass("active")) {

            obj.removeClass("active");
        } else {

            obj.addClass("active");
        }
    }
}
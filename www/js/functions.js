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

    response = new AvailabilityResponse(-2, "");

    $.ajax({
        url: "/api/check-availability/" + type + "/",
        data: {
            "value": val
        },
        async: false,
        method: "POST",
        success: function (r) {
            response = new AvailabilityResponse(r.status, r.message);
            return response;
        },
        error: function (r) {
            response = new AvailabilityResponse(-2, "");
        }
    })
    var status = response.getStatus();
    if (status === -1 || status === -2) {
        console.info("something went wrong while checking " + type)
        return true;
    }
    return (status === 1);
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

AvailabilityResponse = function (status, message) {
    this.message = message;
    this.status = status;

    this.getStatus = function () {
        return this.status;
    }

    this.getMessage = function () {
        return this.message;
    }
}

//SingUp and profile change check

/**
 * @return {boolean}
 */
Nette.validators.username_rule3 = function (elem, args, val) {
    return checkAvailability("username", val);
}

/**
 * @return {boolean}
 */
Nette.validators.email_rule2 = function (elem, args, val) {
    return checkAvailability("email", val);
};

Nette.validators.password_1_rule1 = function (elem, args, val) {
    var result = new RegExp(/[0-9](.)*[0-9]/).test(val) && new RegExp(/[a-z](.)*[a-z]/).test(val) && new RegExp(/[A-Z](.)*[A-Z]/).test(val);
    return result;
}

Nette.validators.username_rule2 = function (elem, args, val) {
    return !(new RegExp(/[^0-9a-zA-Z-_+]/).test(val));
}
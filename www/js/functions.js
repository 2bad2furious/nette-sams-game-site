/**
 * Created by user on 26.07.2017.
 */

serverlog = function (data) {
    console.info(data);
    $.post("/api/log", {
            "data": data
        },
        function (r) {
            console.log(r.success);
        }
    )
}

//https://css-tricks.com/snippets/jquery/animate-heightwidth-to-auto/
jQuery.fn.animateAuto = function (prop, speed, callback) {
    var elem, height, width;
    return this.each(function (i, el) {
        el = jQuery(el), elem = el.clone().css({"height": "auto", "width": "auto"}).appendTo("body");
        height = elem.css("height"),
            width = elem.css("width"),
            elem.remove();

        if (prop === "height")
            el.animate({"height": height}, speed, callback);
        else if (prop === "width")
            el.animate({"width": width}, speed, callback);
        else if (prop === "both")
            el.animate({"width": width, "height": height}, speed, callback);
    });
}
jQuery.fn.getHeightWidthInitial = function () {
    var el = jQuery(this);
    var elem = el.clone().css({"height": "auto", "width": "auto"}).appendTo("body");
    var height = elem.css("height"),
        width = elem.css("width");
    elem.remove();

    console.log(height, width);
    return {
        "height": height,
        "width": width
    }
}

setHeaderHeightInitial = function () {
    headerHeightInitial = $("#header-container").getHeightWidthInitial().height,
        resizeChangesDone = true;
}

checkLinksForBeingActive = function () {
    curLink = window.location.pathname,
        $("a").each(function (i) {
            iter = $(this)
            link = iter.attr("href");
            if (curLink === link) iter.addClass("disabled");
            if (curLink.split(link).length === 2) iter.addClass("active");
        })
}

checkAvailability = function (type, val, elem) {
    if (typeof type !== "string" || typeof val !== "string")
        throw new Error("wrong parameters passed");
    if (type !== "email" && type !== "username")
        throw new Error(type + " is not supported");

    $.ajax({
        url: "/api/check-availability/" + type + "/",
        data: {
            "value": val
        },
        method: "POST",
        success: function (r) {
            console.info(r, typeof r.status !== "undefined", r.status === 0);
            if (typeof r.status !== "undefined" && r.status === 0) {
                LiveForm.addError(elem, r.message)
            }
        },
        error: function () {
            console.info("There was en error while checking " + type)
        }
    })
}

toggleMenu = function (speed) {
    var obj = $("ul#header-container");
    if (obj.length) {
        if (obj.hasClass("active")) {
            obj.animate({
                height: 0
            }, speed)
            obj.removeClass("active");
        } else {
            if (!resizeChangesDone) {
                setHeaderHeightInitial();
            }
            obj.animate({
                height: headerHeightInitial
            }, speed);
            obj.addClass("active");
        }
    }
}

/**
 * @return {boolean}
 */
Nette.validators.username_rule3 = function (elem, args, val) {
    checkAvailability("username", val, elem);
    return null;
}

/**
 * @return {boolean}
 */
Nette.validators.email_rule2 = function (elem, args, val) {
    checkAvailability("email", val, elem);
    return null;
};

Nette.validators.password_1_rule1 = function (elem, args, val) {
    var result = new RegExp(/[0-9](.)*[0-9]/).test(val) && new RegExp(/[a-z](.)*[a-z]/).test(val) && new RegExp(/[A-Z](.)*[A-Z]/).test(val);
    return result;
}

Nette.validators.username_rule2 = function (elem, args, val) {
    return !(new RegExp(/[^0-9a-zA-Z-_+]/).test(val));
}
/**
 * Created by user on 19.07.2017.
 */
$(document).ready(function () {
    checkLinksForBeingActive();

    //init nette.ajax
    $(function () {
        $.nette.init();
    });

    $(".no-js").addClass("js");

    bindEmailChecker();
    bindUsernameChecker();

    $("a.disabled").click(function (e) {
        e.preventDefault();
    })

    $("span#header_menu_roll").click(function () {
        toggleMenu();
    });

});
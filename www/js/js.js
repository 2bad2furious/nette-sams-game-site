/**
 * Created by user on 19.07.2017.
 */
$(document).ready(function () {
    checkLinksForBeingActive();

    LiveForm.setOptions({
        showAllErrors: true,
        wait: 50
    });

    $(".no-js").addClass("js");

    $("a.disabled").click(function (e) {
        e.preventDefault();
    })

    $("span#header_menu_roll").click(function () {
        toggleMenu();
    });
});
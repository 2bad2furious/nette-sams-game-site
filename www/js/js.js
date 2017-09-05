/**
 * Created by user on 19.07.2017.
 */
var resizeTimeout = 0;
var resizeChangesDone = false;
var headerHeightInitial = 0;

$(window).resize(function () {
    resizeChangesDone = false;
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function () {
        setHeaderHeightInitial();
    }, 100)
})

$(document).ready(function () {
    checkLinksForBeingActive();

    LiveForm.setOptions({
        showAllErrors: true,
        wait: 100,
        showValid: true
    });

    $(function () {
        $.nette.init();
    });

    $(function () {
        var elements = $(".no-js");
        elements.addClass("js");
        elements.removeClass("no-js");
    });

    $("a.disabled").click(function (e) {
        e.preventDefault();
    })

    $("a#header_menu_roll").click(function () {
        toggleMenu(100);
    });
});
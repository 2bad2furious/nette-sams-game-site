/**
 * Created by user on 19.07.2017.
 */
$(document).ready(function () {
    checkLinksForBeingActive();

    //init nette.ajax
    $(function () {
        $.nette.init();
    });

    bindEmailChecker();
    bindUsernameChecker();

    $("a.disabled").click(function (e) {
        e.preventDefault();
    })

});
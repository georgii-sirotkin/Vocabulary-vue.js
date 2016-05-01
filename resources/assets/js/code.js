$(window).scroll(function () {
    if ($(window).width() >= 768) {
        if ($(this).scrollTop() > 39) {
            $(".masthead").fadeOut();
        } else {
            $(".masthead").fadeIn();
        }
    }
});
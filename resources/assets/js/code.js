$(window).scroll(function () {
    if ($(window).width() >= 768) {
        if ($(this).scrollTop() > 39) {
            $(".masthead").fadeOut();
        } else {
            $(".masthead").fadeIn();
        }
    }
});

// functions used in random.blade.php

function getClass(statusCode) {
	switch (statusCode) {
		case 0:
			className = 'text-danger';
			break;
		case 1:
			className = 'text-success';
			break;
		case 2:
			className = 'text-warning';
			break;
		default:
			className = '';
	}
	return className;
}

function changePageHeader(newValue) {
	$(".page-header h3").fadeOut("fast", function () {
		$(".page-header h3").html(newValue);
		$(".page-header h3").fadeIn("normal");
	});
}

function showResponse(data) {
	$("#formArea").fadeOut("fast", function () {
		$("#responseMessage").html(data.message);
		$("#responseMessage").addClass(getClass(data.statusCode));
		$("#responseArea").fadeIn("normal");
	});
}

function showNextWord(html) {
	$(".sheet").children().fadeOut("normal", function () {
    	$(".page-header h3").html($("#pageHeaderTemplate").html());
    	$("#content").html(html);
    	$(".sheet").children().fadeIn("normal");
    });
}


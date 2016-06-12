$(window).scroll(function () {
    if ($(window).width() >= 768) {
        if ($(this).scrollTop() > 39) {
            $(".masthead").fadeOut();
        } else {
            $(".masthead").fadeIn();
        }
    }
});

// Set focus and move cursor to the end of input
function setFocusOnInput(jQueryObject) {
	jQueryObject.focus();
	var tempStr = jQueryObject.val();
	jQueryObject.val(tempStr);
}

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
	$(".page-header h3").html(newValue);
}

function processAnswer (form) {
	$("#formArea").fadeTo("fast", 0);
	$(".page-header h3").fadeTo("fast", 0);
	$.post(form.attr('action'), form.serialize(), null, 'json')
	.done(function(data) {
		$("#formArea").promise().done(function() {showResponse(data)});
		$(".page-header h3").promise().done(function() {
			changePageHeader(escapeHtml(data.correctAnswer));
			$(".page-header h3").fadeTo("normal", 1);
		});
	});
}

function showResponse(data) {
	$("#responseMessage").html(data.message);
	$("#responseMessage").addClass(getClass(data.statusCode));
	$("#responseArea").fadeTo("normal", 1);
	processingAnswer = false;
}

function loadNextWord(pageUrl) {
	$(".sheet").children().fadeTo("fast", 0);
	$.ajax({
		url: pageUrl,
		dataType: "html",
		cache: false
	})
	.done(function(html) {
		$(".sheet").children().promise().done(function() {showNextWord(html)});
	});
}

function showNextWord(html) {
	changePageHeader($("#pageHeaderTemplate").html());
	$("#content").html(html);
	$(".sheet").children().fadeTo("normal", 1, function () {
		setFocusOnInput($("#answer"));
		loadingNextWord = false;
	});
}

var entityMap = {
		"&": "&amp;",
		"<": "&lt;",
		">": "&gt;",
		'"': '&quot;',
		"'": '&#39;',
		"/": '&#x2F;'
	};

function escapeHtml(string) {
	return String(string).replace(/[&<>"'\/]/g, function (s) {
		return entityMap[s];
	});
}
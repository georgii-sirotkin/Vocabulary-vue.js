// Set focus and move cursor to the end of input
var input = $("#wordInput");
input.focus();
var tempStr = input.val();
input.val(tempStr);

// Add definition input.
$("#addDefinitionButton").click(function() {
	if ($('#definitionsArea').is(':hidden')) {
		$("#definitionsContainer").append($("#definitionTemplate").html());
		$('#definitionsArea').show("slow");
		return;
	}
	
    $($("#definitionTemplate").html()).hide().appendTo("#definitionsContainer").slideDown();
});

// Remove definition input.
$("#definitionsContainer").on("click", ".deleteDefinition", function () {
    if ($("#definitionsContainer").children().length == 1) {
    	$('#definitionsArea').hide("slow", function () { $("#definitionsContainer").empty(); });
    	return;
    }

    $( this ).parents(':eq(1)').slideUp("normal", function () { $(this).remove(); });
});

// Disable tabpanel input that has just been hidden.
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $( $( e.relatedTarget ).attr('href') ).find('input').prop( "disabled", true );
})

// Enable tabpanel input that is about to be shown.
$('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
    $( $( e.relatedTarget ).attr('href') ).find('input').prop( "disabled", false );
})

// Remove old image and show image input.
$("#deleteOldImage").click(function () {
    $("#oldImage").slideUp("normal", function () { $(this).remove(); });
    $("#imageInput").slideDown();
});
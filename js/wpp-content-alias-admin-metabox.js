jQuery(document).ready(function($){
	$('.wppca-add-row').on('click', function() {
		console.log('something clicked');
		var row = $('#wppca-empty-row').clone(true);
		row.removeAttr('id');
		row.removeClass('empty-row screen-reader-text');
		row.insertBefore('#wppca-empty-row');
		return false;
	});
	$('.wppca-remove-row').on('click', function() {
		var agree = confirm("Are you sure you want to remove the alias?");
		if(agree) {
			$(this).parents('tr').remove();
		}
		return false;
	});
	$('#wppca-save-button').click(function(e) {
		e.preventDefault();
		$('#publish').click();
	});
});
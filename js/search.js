jQuery(document).ready(function($) {
	
	$('#find-shortcode #search-button').on('click', function(e) {
		var $btn = $(this);
		var list_all = 0;
		
		e.preventDefault();
		
		$btn.prop('disabled', true);
		
		if ($('#lis-all-shortcodes').is(':checked')) {
    	    list_all = 1;	
		}
		
		var data={
			'action' : 'find_shortcode',
			'term' : $('#find-shortcode #shortcode-search').val(),
			'list_all' : list_all
		};

		$.post(ajaxurl, data, function(response) {
			$('#find-shortcode-results').html(response);
			
			$btn.prop('disabled', false);		
		});
	});
	
});
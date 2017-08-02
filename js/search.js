jQuery(document).ready(function($) {
	
	$('#find-shortcode #search-button').on('click', function(e) {
		e.preventDefault();
		
		var data={
			'action' : 'find_shortcode',
			'term' : $('#find-shortcode #shortcode-search').val()
		};

		$.post(ajaxurl, data, function(response) {
			$('#find-shortcode-results').html(response);		
		});
	});
	
});
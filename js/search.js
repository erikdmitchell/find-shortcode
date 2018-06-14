jQuery(document).ready(function($) {
	
	$('#find-shortcode #search-button').on('click', function(e) {
		var $btn = $(this);
		
		e.preventDefault();
		
		$btn.prop('disabled', true);
		
		var data={
			'action' : 'find_shortcode',
			'term' : $('#find-shortcode #shortcode-search').val()
		};

		$.post(ajaxurl, data, function(response) {
			$('#find-shortcode-results').html(response);
			
			$btn.prop('disabled', false);		
		});
	});
	
});
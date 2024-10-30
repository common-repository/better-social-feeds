jQuery(document).ready(function ($) {
	
	$('#bsft_pro_connect_threads_btn').on('click', function(){
		
		const redirectURI = $('#fftmj_redirect_uri').val();
		
		const client_id = $(this).attr('data-client-id');
		
		const url = 'https://threads.net/oauth/authorize?client_id='+client_id+'&redirect_uri='+redirectURI+'&scope=threads_basic&response_type=code';
		
		window.location.href = url;
		
		// const childWindow = window.open(url, 'Connect your Account', 'width=710,height=555'); // ,left=160,top=170
		
	});
	
});
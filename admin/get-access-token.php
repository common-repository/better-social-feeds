<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
   exit;
}


function fftmj_threads_connect(){
	
	// check for threads returned code and request short-lived access token
	if($_GET){
		if(isset($_GET['code'])){
			$fftmj_threads_code = sanitize_text_field($_GET['code']);
			$fftmj_threads_code = str_replace("#_", "", $fftmj_threads_code);
			
			$fftmj_threads_tkn_url = 'https://graph.threads.net/oauth/access_token';
			
			$fftmj_client_id 		= get_option('fftmj_client_id');
			$fftmj_client_secret	= get_option('fftmj_client_secret');
			$fftmj_redirect_uri		= get_option('fftmj_redirect_uri');
			
			$fftmj_data = array(
				'client_id'			=> $fftmj_client_id,
				'client_secret'		=> $fftmj_client_secret,
				'code'				=> $fftmj_threads_code,
				'grant_type'		=> 'authorization_code',
				'redirect_uri'		=> $fftmj_redirect_uri
			);
			
			$fftmj_threads_tkn_url_args = array(
				'body'    => $fftmj_data
			);
			
			$fftmj_access_token = wp_remote_post($fftmj_threads_tkn_url, $fftmj_threads_tkn_url_args);
			
			if ( is_wp_error( $fftmj_access_token ) ) {
				$error_message = $fftmj_access_token->get_error_message();
				echo "Something went wrong: " . $error_message;
			}
			else {
				if(isset($fftmj_access_token['body'])){
					
					$fftmj_access_token_obj = json_decode( $fftmj_access_token['body'] );
					
					if(isset( $fftmj_access_token_obj->error_message )){
						echo 'Error getting response from Threads: ' . $fftmj_access_token_obj->error_message;
					}
					else{
						if (isset($fftmj_access_token_obj->access_token)){
							$fftmj_short_acs_tkn = $fftmj_access_token_obj->access_token;
							
							// update_option('fftmj_api_short_token', $fftmj_short_acs_tkn);
							
							// Get long lived access token from short lived token
							$fftmj_ll_token_data = array(
								'body'    => array(
									'client_secret'		=> $fftmj_client_secret,
									'grant_type'		=> 'th_exchange_token',
									'access_token'		=> $fftmj_short_acs_tkn
								)
							);
							$fftmj_llad_response = wp_remote_get('https://graph.threads.net/access_token', $fftmj_ll_token_data);
							
							if ( is_wp_error( $fftmj_llad_response ) ) {
								$error_message = $fftmj_llad_response->get_error_message();
								echo "Something went wrong for long lived token: " . $error_message;
							}
							else{
								
								if(isset($fftmj_llad_response['body'])){
									$fftmj_llad_token_obj = json_decode( $fftmj_llad_response['body'] );
									
									if(isset( $fftmj_llad_token_obj->error_message )){
										echo 'Error getting long lived token from Threads: ' . $fftmj_llad_token_obj->error_message;
									}
									else{
										
										// get the long lived token and store in DB
										if (isset($fftmj_llad_token_obj->access_token)){
											
											$fftmj_long_lived_acs_tkn = $fftmj_llad_token_obj->access_token;
											$fftmj_long_lived_acs_tkn_expires = $fftmj_llad_token_obj->expires_in;
											
											$fftmj_api_long_token_generated = Date("Y-m-d h:m:s");
											$fftmj_api_long_token_expires = date('Y-m-d H:i:s', strtotime(' +'.$fftmj_long_lived_acs_tkn_expires.' seconds '));
											
											update_option('fftmj_api_long_token', $fftmj_long_lived_acs_tkn);
											update_option('fftmj_api_long_token_generated', $fftmj_api_long_token_generated);
											update_option('fftmj_api_long_token_expires', $fftmj_api_long_token_expires);
											
										}
										
									}
									
								}
								
							}
							
						}
						else{
							echo 'Short lived Access Token was not returned';
						}
					}
					
				}
			}
			
		}
		
		
	}
	
}

// get a long-lived refresh token after 24 hours and before 60 days of original long-lived token
function fftmj_threads_refresh_token($current_ll_access_token){
	
	$fftmj_ref_token_data = array(
		'body'    => array(
			'grant_type'		=> 'th_refresh_token',
			'access_token'		=> $current_ll_access_token
		)
	);
	$fftmj_ref_token_response = wp_remote_get('https://graph.threads.net/refresh_access_token', $fftmj_ref_token_data);
	
	if ( is_wp_error( $fftmj_ref_token_response ) ) {
		$error_message = $fftmj_ref_token_response->get_error_message();
		// echo "Something went wrong while refreshing token: " . $error_message;
		return false;
	}
	else{
		
		if(isset($fftmj_ref_token_response['body'])){
			$fftmj_ref_token_obj = json_decode( $fftmj_ref_token_response['body'] );
			
			if(isset( $fftmj_ref_token_obj->error_message )){
				// echo 'Error refreshing token from Threads: ' . $fftmj_ref_token_obj->error_message;
				return false;
			}
			else{
				
				// get the long lived token and store in DB
				if (isset($fftmj_ref_token_obj->access_token)){
					
					$fftmj_long_lived_acs_tkn = $fftmj_ref_token_obj->access_token;
					$fftmj_long_lived_acs_tkn_expires = $fftmj_ref_token_obj->expires_in;
					
					$fftmj_api_long_token_generated = Date("Y-m-d h:m:s");
					$fftmj_api_long_token_expires = date('Y-m-d H:i:s', strtotime(' +'.$fftmj_long_lived_acs_tkn_expires.' seconds '));
					
					update_option('fftmj_api_long_token', $fftmj_long_lived_acs_tkn);
					update_option('fftmj_api_long_token_generated', $fftmj_api_long_token_generated);
					update_option('fftmj_api_long_token_expires', $fftmj_api_long_token_expires);
					
					return true;
					
				}
				else{
					// echo 'Error refreshing token from Threads. No token returned. Please try again laater.';
					return false;
				}
				
			}
			
		}
		else{
			// echo 'Error refreshing token from Threads - No body in response.';
			return false;
		}
		
	}
	
	
}


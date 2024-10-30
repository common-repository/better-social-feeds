<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
   exit;
}

if (!function_exists('fftmj_config_callback')) {
	function fftmj_config_callback(){
		
		if (!current_user_can('manage_options')){
			wp_die( __('You do not have sufficient permissions to access this page.', 'better-social-feeds') );
		}
		
		// Connect Threads account
		fftmj_threads_connect();
		
		if($_POST){
			
			// Disconnect Threads Account
			if(isset($_POST['fftmj_acnt_disconnect_submit'])){
				$nonce = sanitize_text_field($_REQUEST['_wpnonce']);
				if (!wp_verify_nonce($nonce, 'fftmj_account_disconnect')) {
					wp_die(__('error - Could not verify POST values', 'better-social-feeds'));
				}
				
				delete_option('fftmj_api_long_token');
				delete_option('fftmj_api_long_token_generated');
				delete_option('fftmj_api_long_token_expires');
				
			}
			
			// Save App Settings
			if(isset($_POST['fftmj_settings_submit'])){

				$nonce = sanitize_text_field($_REQUEST['_wpnonce']);
				if (!wp_verify_nonce($nonce, 'fftmj_settings_option')) {
					wp_die(__('error - Could not verify POST values', 'better-social-feeds'));
				}
				
				if(isset($_POST['fftmj_client_id'])){
					$fftmj_username = sanitize_text_field($_POST['fftmj_client_id']);
					update_option('fftmj_client_id', $fftmj_username);
				}
				
				if(isset($_POST['fftmj_client_secret'])){
					$fftmj_username = sanitize_text_field($_POST['fftmj_client_secret']);
					update_option('fftmj_client_secret', $fftmj_username);
				}
				
				if(isset($_POST['fftmj_redirect_uri'])){
					$fftmj_username = sanitize_text_field($_POST['fftmj_redirect_uri']);
					update_option('fftmj_redirect_uri', $fftmj_username);
				}
				
				// Saved message
				echo '<div class="notice notice-success is-dismissible"><p>Settings Saved!</p></div>';
				
			}
		}
		
		
	?>
		<!-- Pages HTMl -->
		<div class="wrap">
			<div id="fftmj_settings">
				
				<div class="fftmj_setting-container">
				
					<h2 class="fftmj_admin_heading">General Settings</h2>
					
					<p style="text-align: center;"> Use Shortcode <strong>[fftmj_feeds]</strong> to load threads from below user.</p>
					
					<?php
					$fftmj_client_id = get_option('fftmj_client_id');
					$fftmj_client_secret = get_option('fftmj_client_secret');
					
					$http_protocol = 'http://';
					if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443){
						$http_protocol = 'https://';
					}
					$fftmj_api_redirect_url = $http_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=fftmj-free';
					
					$fftmj_need_auth = true;
					$fftmj_api_long_token_expires = get_option('fftmj_api_long_token_expires');
					if($fftmj_api_long_token_expires){
						
						$fftmj_api_long_token = get_option('fftmj_api_long_token');
						
						$fftmj_at_generated = get_option('fftmj_api_long_token_generated');
						$fftmj_at_generated_next_date = strtotime( date('Y-m-d H:i:s', strtotime(' +25 hours ')) );
						
						$fftmj_at_expires_on = strtotime($fftmj_api_long_token_expires); 
						$fftmj_today = strtotime( Date("Y-m-d h:m:s") );
						
						if($fftmj_today < $fftmj_at_generated_next_date){
							$fftmj_need_auth = false;
						}
						else{
							// 25 hours have been passed, we need to refresh the token before 60 days.
							if($fftmj_today < $fftmj_at_expires_on){
								
								$fftmj_token_refreshed = fftmj_threads_refresh_token($fftmj_api_long_token);
								if($fftmj_token_refreshed){
									$fftmj_need_auth = false;
								}
								
							}
							else{
								
								// Access token expired and can not be refreshed. Need Reauthroization.
								$fftmj_need_auth = true;
								
							}
							
						}
						
						
					}
					
					if( $fftmj_need_auth ){
						if($fftmj_client_id != '' && $fftmj_client_secret != ''){
							echo '<div class="fftmj_inner-container">
								<div class="fftmj_col-1">
									<label>Threads Account Not Connected.</label>
								</div>
								<div class="fftmj_col-2">
									<div id="bsft_pro_connect_threads_btn" class="button-primary" data-client-id="'.$fftmj_client_id.'">Connect Now</div>
								</div>
							</div>';
						}
					}
					else{
						
						echo '<div class="fftmj_inner-container">
							<div class="fftmj_col-1">
								<label>Threads Account Connected.</label>
							</div>
							<div class="fftmj_col-2">
								<form method="POST" action="'.$fftmj_api_redirect_url.'">';
									wp_nonce_field('fftmj_account_disconnect');
						echo		'<input type="submit" name="fftmj_acnt_disconnect_submit" class="button-primary" value="Disconnect"/>
								</form>
							</div>
						</div>';
					}
					
					?>
					
					<!-- Threads App Settings -->
					<form method="POST" action="<?php echo $fftmj_api_redirect_url; ?>">
						<?php 
						wp_nonce_field('fftmj_settings_option');
						?>
						
						<div class="fftmj_inner-container">
							<div class="fftmj_col-1">
								<label for="fftmj_client_id">Threads Client ID</label>
							</div>
							<div class="fftmj_col-2">
								<input type="text" name="fftmj_client_id" id="fftmj_client_id" class="fftmj_input" placeholder="Client ID from your App" value="<?php echo esc_html( $fftmj_client_id ); ?>">
							</div>								
						</div>
						
						<div class="fftmj_inner-container">
							<div class="fftmj_col-1">
								<label for="fftmj_client_secret">Threads Client Secret</label>
							</div>
							<div class="fftmj_col-2">
								<input type="text" name="fftmj_client_secret" id="fftmj_client_secret" class="fftmj_input" placeholder="Client Secret from your App" value="<?php echo esc_html( $fftmj_client_secret ); ?>">
							</div>								
						</div>
						
						<div class="fftmj_inner-container">
							<div class="fftmj_col-1">
								<label for="fftmj_redirect_uri">Threads Redirect URL</label>
							</div>
							<div class="fftmj_col-2">
								<input type="text" name="fftmj_redirect_uri" id="fftmj_redirect_uri" readonly class="fftmj_input" value="<?php echo esc_html( $fftmj_api_redirect_url ); ?>">
							</div>								
						</div>
						
						
						<div class="fftmj_inner-container">
							<div class="fftmj_col-1">
								<div class="fftmj_admin_save">
									<input type="submit" name="fftmj_settings_submit" class="button-primary" value="Save">
								</div>
							</div>
						</div>
						
					</form>
				
				</div>
					
			</div>
		</div>
	
	<?php
	}
}

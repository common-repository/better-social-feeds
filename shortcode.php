<?php
/*
* Shortcode to display new post form
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
   exit;
}

// Fetch all threads
add_shortcode('fftmj_feeds', 'fftmj_feeds_callback');
if (!function_exists('fftmj_feeds_callback')) {
	function fftmj_feeds_callback($atts){
		if( !is_admin() ){ // only run when in front-end
			
			wp_enqueue_style('fftmj-style');
			
			// $userid = $url = '';
			// if($atts && isset($atts['userid'])){
				// $userid = $atts['userid'];
			// }
			// elseif($atts && isset($atts['url'])){
				// $userid = $atts['url'];
			// }
			
			// $html = '<div id="fftmj_threads_container" class="fftmj_threads_cont">
						// <div class="fftmj_loading">Loading Feeds From Threads...</div>
						// <input type="hidden" id="threads_user" value="' . esc_html( $userid ) . '">
					// </div>';
			
			// return $html;
			
			
			// Using API
			$fftmj_threads_api_url = 'https://graph.threads.net/v1.0/';
			$fftmj_api_long_token = get_option('fftmj_api_long_token');
			$fftmj_threads_user_url = $fftmj_threads_api_url . 'me/threads';
			
			// &since=2023-10-15&until=2023-11-18&limit=1
			
			if(!$fftmj_api_long_token){
				return 'Error - No Access Token found. Please fix from settings.';
			}
			
			$fftmj_threads_data = array(
				'body'	=> array(
					'access_token'		=> $fftmj_api_long_token,
					// 'threads-user-id'	=> 'me',
				)
			);
			
			$fftmj_threads_media_data = array(
				'body'	=> array(
					'access_token'		=> $fftmj_api_long_token,
					'threads-user-id'	=> 'me',
					'metric'			=> 'views, likes, replies, reposts, quotes'
					
				)
			);
			
			// get profile info
			$fftmj_profile_res = wp_remote_get($fftmj_threads_api_url . 'me?fields=id,username,threads_profile_picture_url,threads_biography', $fftmj_threads_data);
			$fftmj_threads_profile = json_decode($fftmj_profile_res['body']);
			
			$fftmj_usr_img = '';
			if($fftmj_threads_profile->threads_profile_picture_url){
				if(isset($fftmj_threads_profile->threads_profile_picture_url)){
					$fftmj_usr_img = $fftmj_threads_profile->threads_profile_picture_url;
				}
			}
			
			// get threads
			$fields = 'id,media_product_type,media_type,text,media_url,permalink,owner,username,timestamp,shortcode,thumbnail_url,children,is_quote_post';
			
			$fftmj_threads_res = wp_remote_get($fftmj_threads_user_url . '?fields=' . $fields, $fftmj_threads_data); // . '&limit=5'
			
			if ( is_wp_error( $fftmj_threads_res ) ) {
				$error_message = $fftmj_threads_res->get_error_message();
				return "Something went wrong: " . $error_message;
			}
			else {
			
				$fftmj_threads_obj = json_decode( $fftmj_threads_res['body'] );
				
				// var_dump($fftmj_threads_obj);
				
				$fftmj_threads = $fftmj_threads_obj->data;
				
				$fftmj_html = '';
				$cnt = 0;
				foreach($fftmj_threads as $fftmj_thread){
					$id = $media_product_type = $media_type = $text = $media_url = $permalink = $owner = $username = $timestamp = $shortcode = $thumbnail_url = $children = $is_quote_post = '';
					if(isset($fftmj_thread->id)){
						$id = $fftmj_thread->id;
					}
					
					// get Insights
					// $fftmj_media_insight_res = wp_remote_get($fftmj_threads_api_url . $id . '/insights', $fftmj_threads_media_data);
					
					// if ( is_wp_error( $fftmj_media_insight_res ) ) {
						// $error_message = $fftmj_media_insight_res->get_error_message();
						// echo "Something went wrong: " . $error_message;
					// }
					// else{
					
						// $fftmj_media_insight_obj = json_decode( $fftmj_media_insight_res['body'] );
						
						// var_dump($fftmj_media_insight_obj);
					// }
					
					
					if(isset($fftmj_thread->media_product_type)){
						$media_product_type = $fftmj_thread->media_product_type;
					}
					if(isset($fftmj_thread->media_type)){
						$media_type = $fftmj_thread->media_type;
					}
					if(isset($fftmj_thread->text)){
						$text = $fftmj_thread->text;
					}
					if(isset($fftmj_thread->media_url)){
						$media_url = $fftmj_thread->media_url;
					}
					if(isset($fftmj_thread->permalink)){
						$permalink = $fftmj_thread->permalink;
					}
					if(isset($fftmj_thread->owner)){
						$owner = $fftmj_thread->owner;
					}
					if(isset($fftmj_thread->username)){
						$username = $fftmj_thread->username;
					}
					if(isset($fftmj_thread->timestamp)){
						$timestamp = $fftmj_thread->timestamp;
					}
					if(isset($fftmj_thread->shortcode)){
						$shortcode = $fftmj_thread->shortcode;
					}
					if(isset($fftmj_thread->thumbnail_url)){
						$thumbnail_url = $fftmj_thread->thumbnail_url;
					}
					if(isset($fftmj_thread->children)){
						$children = $fftmj_thread->children;
					}
					if(isset($fftmj_thread->is_quote_post)){
						$is_quote_post = $fftmj_thread->is_quote_post;
					}
					
					$rp_time_passed = fftmj_api_time_elapsed_string($timestamp);
					
					
					if( $media_type == 'REPOST_FACADE' || ($media_type == 'TEXT_POST' && $text == '') ){
						continue;
					}
					
					if($cnt < 5){
					
						$item_html = '';
						
						$item_html .= '<div class="fftmj_single_thread">';
						
							$item_html .= '<div class="fftmj_row1">';
							
								if($fftmj_usr_img){
									$item_html .= 	'<img class="fftmj_profileimage" src="'.$fftmj_usr_img.'">';
								}
								
								$item_html .= '<div class="fftmj_thrusr"><a href="https://www.threads.net/@'.$username.'" target="_blank">' . $username . '</a></div>';
								
								$item_html .= '<div class="fftmj_time">'.$rp_time_passed.'</div>';
								$item_html .= '<div class="fftmj_thrlogo"><svg aria-label="Threads" class="x1ypdohk x13dflua x11xpdln xus2keu xk4oym4" fill="#fff" height="25px" role="img" viewBox="0 0 192 192" width="100%" xmlns="http://www.w3.org/2000/svg"><path class="x19hqcy" d="M141.537 88.9883C140.71 88.5919 139.87 88.2104 139.019 87.8451C137.537 60.5382 122.616 44.905 97.5619 44.745C97.4484 44.7443 97.3355 44.7443 97.222 44.7443C82.2364 44.7443 69.7731 51.1409 62.102 62.7807L75.881 72.2328C81.6116 63.5383 90.6052 61.6848 97.2286 61.6848C97.3051 61.6848 97.3819 61.6848 97.4576 61.6855C105.707 61.7381 111.932 64.1366 115.961 68.814C118.893 72.2193 120.854 76.925 121.825 82.8638C114.511 81.6207 106.601 81.2385 98.145 81.7233C74.3247 83.0954 59.0111 96.9879 60.0396 116.292C60.5615 126.084 65.4397 134.508 73.775 140.011C80.8224 144.663 89.899 146.938 99.3323 146.423C111.79 145.74 121.563 140.987 128.381 132.296C133.559 125.696 136.834 117.143 138.28 106.366C144.217 109.949 148.617 114.664 151.047 120.332C155.179 129.967 155.42 145.8 142.501 158.708C131.182 170.016 117.576 174.908 97.0135 175.059C74.2042 174.89 56.9538 167.575 45.7381 153.317C35.2355 139.966 29.8077 120.682 29.6052 96C29.8077 71.3178 35.2355 52.0336 45.7381 38.6827C56.9538 24.4249 74.2039 17.11 97.0132 16.9405C119.988 17.1113 137.539 24.4614 149.184 38.788C154.894 45.8136 159.199 54.6488 162.037 64.9503L178.184 60.6422C174.744 47.9622 169.331 37.0357 161.965 27.974C147.036 9.60668 125.202 0.195148 97.0695 0H96.9569C68.8816 0.19447 47.2921 9.6418 32.7883 28.0793C19.8819 44.4864 13.2244 67.3157 13.0007 95.9325L13 96L13.0007 96.0675C13.2244 124.684 19.8819 147.514 32.7883 163.921C47.2921 182.358 68.8816 191.806 96.9569 192H97.0695C122.03 191.827 139.624 185.292 154.118 170.811C173.081 151.866 172.51 128.119 166.26 113.541C161.776 103.087 153.227 94.5962 141.537 88.9883ZM98.4405 129.507C88.0005 130.095 77.1544 125.409 76.6196 115.372C76.2232 107.93 81.9158 99.626 99.0812 98.6368C101.047 98.5234 102.976 98.468 104.871 98.468C111.106 98.468 116.939 99.0737 122.242 100.233C120.264 124.935 108.662 128.946 98.4405 129.507Z"></path></svg></div>';
								
							$item_html .= '</div>'; // row-1
						
						$item_html .= '<div class="fftmj_row2">';
							$item_html .= '<div class="fftmj_title">' . $text . '</div>';
						$item_html .= '</div>'; // row-2
						
						$item_html .= '<div class="fftmj_row3">';
						if($media_url){
							
							if($media_type == 'IMAGE' || $media_type == 'CAROUSEL_ALBUM'){ // TEXT_POST, IMAGE, VIDEO, CAROUSEL_ALBUM, AUDIO, or REPOST_FACADE
								$item_html .= '<a href="'.$permalink.'" target="_blank"><img class="fftmj_mediaimage" src="' . $media_url . '"></a>';
							}
							elseif($media_type == 'VIDEO'){
								$item_html .= '<video class="fftmj_mediaimage" controls><source src="'.$media_url.'" type="video/mp4">';
							}
							
						}
						
						// get quoted html here
						
						$item_html .= '</div>'; // row-3
						
						$item_html .= '<div class="fftmj_row4">';
							$item_html .= '<div class="fftmj_likereply"><a href="'.$permalink.'" target="_blank"></a></div>'; // not available yet - ' . $likes . ($rplies ? " · " : "") . $rplies . '
							$item_html .= '<div class="fftmj_thricons">';
								$item_html .= '<a href="'.$permalink.'" target="_blank"><svg aria-label="Like" class="x1lliihq x1n2onr6" color="rgb(0, 0, 0)" fill="transparent" height="19" role="img" viewBox="0 0 24 22" width="20"><title>Like</title><path d="M1 7.66c0 4.575 3.899 9.086 9.987 12.934.338.203.74.406 1.013.406.283 0 .686-.203 1.013-.406C19.1 16.746 23 12.234 23 7.66 23 3.736 20.245 1 16.672 1 14.603 1 12.98 1.94 12 3.352 11.042 1.952 9.408 1 7.328 1 3.766 1 1 3.736 1 7.66Z" stroke="currentColor" stroke-width="2"></path></svg></a>';
								$item_html .= '<a href="'.$permalink.'" target="_blank"><svg aria-label="Comment" class="x1lliihq x1n2onr6" color="rgb(0, 0, 0)" fill="rgb(0, 0, 0)" height="20" role="img" viewBox="0 0 24 24" width="20"><title>Comment</title><path d="M20.656 17.008a9.993 9.993 0 1 0-3.59 3.615L22 22Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></path></svg></a>';
								$item_html .= '<a href="'.$permalink.'" target="_blank"><svg aria-label="Repost" class="x1lliihq x1n2onr6" color="rgb(0, 0, 0)" fill="rgb(0, 0, 0)" height="20" role="img" viewBox="0 0 24 24" width="20"><title>Repost</title><path d="M19.998 9.497a1 1 0 0 0-1 1v4.228a3.274 3.274 0 0 1-3.27 3.27h-5.313l1.791-1.787a1 1 0 0 0-1.412-1.416L7.29 18.287a1.004 1.004 0 0 0-.294.707v.001c0 .023.012.042.013.065a.923.923 0 0 0 .281.643l3.502 3.504a1 1 0 0 0 1.414-1.414l-1.797-1.798h5.318a5.276 5.276 0 0 0 5.27-5.27v-4.228a1 1 0 0 0-1-1Zm-6.41-3.496-1.795 1.795a1 1 0 1 0 1.414 1.414l3.5-3.5a1.003 1.003 0 0 0 0-1.417l-3.5-3.5a1 1 0 0 0-1.414 1.414l1.794 1.794H8.27A5.277 5.277 0 0 0 3 9.271V13.5a1 1 0 0 0 2 0V9.271a3.275 3.275 0 0 1 3.271-3.27Z"></path></svg></a>';
								$item_html .= '<a href="'.$permalink.'" target="_blank"><svg aria-label="Share" class="x1lliihq x1n2onr6" color="rgb(0, 0, 0)" fill="rgb(0, 0, 0)" height="20" role="img" viewBox="0 0 24 24" width="20"><title>Share</title><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="22" x2="9.218" y1="3" y2="10.083"></line><polygon fill="none" points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon></svg></a>';
							$item_html .= '</div>';
						$item_html .= '</div>'; // row-4
						
						$item_html .= '</div>'; // fftmj_single_thread - main
						
						$fftmj_html .= $item_html;
						
						$cnt++;
						
					}
					
				} // for each for thread items
			
			}
			
			// echo '<br> Profile: <br>';
			
			return $fftmj_html;
			
		}
	}
}

// Calculate time passed from the given date-time
if (!function_exists('fftmj_api_time_elapsed_string')) {
	function fftmj_api_time_elapsed_string($datetime) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
			'y' => 'y',
			'm' => 'mon',
			'w' => 'w',
			'd' => 'd',
			'h' => 'h',
			'i' => 'm',
			's' => 's',
		);
		$key_set = '';
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . $v;
				$key_set = $k;
				break;
			}
			else {
				unset($string[$k]);
			}
		}
		return $string[$key_set];
	}
}

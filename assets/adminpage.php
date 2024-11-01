<?php defined('ABSPATH') or die('No script kiddies please!');
/**
 * @package WordPress
 * @subpackage WebP
 *
 * @author RapidDev
 * @copyright Copyright (c) 2019-2020, RapidDev
 * @link https://www.rdev.cc/webp
 * @license https://opensource.org/licenses/MIT
 */
	//All images
	$query_all_images = array(
		'post_type' => 'attachment',
		'post_mime_type' => array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
		),
		'post_status' => 'inherit',
		'posts_per_page' => -1,
	);
	$query_img = new WP_Query($query_all_images);

	$count = $jpeg_count = $webp_count = 0;
	$images_count = $query_img->post_count;

	foreach ($query_img->posts as $image) {
		if($image->post_mime_type == 'image/jpeg'){
			$jpeg_count++;

			$webp = get_post_meta($image->ID, '_webp_alternative', true);
			if($webp != NULL || $webp != '')
			{
				$webp_count++;
			}
		}
	}

	//$alt = get_post_meta ( $image_id, '_wp_attachment_image_alt', true );
	//echo '<img alt="' . esc_html ( $alt ) . '" src="URL HERE" />';
?>
	
	
	<div id="webp-panel">
		<div id="webp-header" style="padding: 40px 15px;background-color: #fff;text-align: center;">
			<h1 style="margin:10px;font-weight:700">WebP</h1>
			<span>panel</span>
		</div>
		<div class="wrap" style="padding:15px;max-width: 800px;margin: 0 auto;">
			<h2><?php _e('Status', RDEV_WEBP_DOMAIN); ?></h2>
			<div style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;display:flex;justify-content: flex-start;">
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;"><?php echo $images_count; ?></h2>
					<span style="color:#767676;"><?php _e('Total images', RDEV_WEBP_DOMAIN) ?></span>
				</div>
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;"><?php echo $jpeg_count; ?></h2>
					<span style="color:#767676;"><?php _e('JPEG images', RDEV_WEBP_DOMAIN) ?></span>
				</div>
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;"><?php echo $webp_count; ?></h2>
					<span style="color:#767676;"><?php _e('WebP Alternatives', RDEV_WEBP_DOMAIN) ?></span>
				</div>
			</div>
			<h2><?php _e('Support', RDEV_WEBP_DOMAIN); ?></h2>
			<div style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;display:flex;justify-content: flex-start;">
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;">JPEG</h2>
					<span style="color:#767676;"><?php echo ($this->jpgactive ? __('supported', RDEV_WEBP_DOMAIN) : __('not supported', RDEV_WEBP_DOMAIN) ) ?></span>
				</div>
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;">PNG</h2>
					<span style="color:#767676;"><?php echo ($this->pngactive ? __('supported', RDEV_WEBP_DOMAIN) : __('not supported', RDEV_WEBP_DOMAIN) ) ?></span>
				</div>
				<div style="width:33.3%">
					<h2 style="margin:0;padding:0;">GIF</h2>
					<span style="color:#767676;"><?php echo ($this->gifactive ? __('supported', RDEV_WEBP_DOMAIN) : __('not supported', RDEV_WEBP_DOMAIN) ) ?></span>
				</div>
			</div>
			<br>
			<h2><?php _e('How to use this plugin on the site?', RDEV_WEBP_DOMAIN); ?></h2>
			<div style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;">
				<h3 style="margin:0;padding:0;">In theme method</h3>
				<span>If you want to use WebP on your WordPress website with this plugin, add image this way:</span>
				<div style="color: #f07178;margin-top: 15px;">
					<span style="color:#0099a7">&lt;?php</span>
					<br>
					&nbsp;&nbsp;&nbsp;&nbsp;$<span style="color:#2a50b7">src <span style="color:#0099a7">=</span> wp_get_attachment_image_src<span style="color:#0099a7">(</span>get_post_thumbnail_id<span style="color:#0099a7">(</span></span>$post->ID <span style="color:#0099a7">)</span>, <span style="color:#0099a7">'</span>full<span style="color:#0099a7">'</span>, false <span style="color:#0099a7">)</span>;
					<br>
					<span style="color:#0099a7">?&gt;</span>
					<br>
					<span style="color:#0099a7">&lt;</span>picture<span style="color:#0099a7">&gt;</span>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#0099a7">&lt;</span>source <span style="color:#f09642">srcset</span><span style="color:#0099a7">="</span><span style="color:#0099a7">&lt;?php</span> <span style="color:#2a50b7">echo</span> $<span style="color:#2a50b7">src;</span><span style="color:#0099a7"> ?&gt;</span><span style="color:#545454;">.webp</span><span style="color:#0099a7">"</span> <span style="color:#f09642">type</span><span style="color:#0099a7">="</span><span style="color:#545454;">image/webp</span><span style="color:#0099a7">"&gt;</span>
					<br>
					&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#0099a7">&lt;</span>source <span style="color:#f09642">srcset</span><span style="color:#0099a7">="</span><span style="color:#0099a7">&lt;?php</span> <span style="color:#2a50b7">echo</span> $<span style="color:#2a50b7">src;</span><span style="color:#0099a7"> ?&gt;</span><span style="color:#0099a7">"</span> <span style="color:#f09642">type</span><span style="color:#0099a7">="</span><span style="color:#545454;">image/jpeg</span><span style="color:#0099a7">"&gt;</span>
					<br>
					&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#0099a7">&lt;</span>img <span style="color:#f09642">src</span><span style="color:#0099a7">="</span><span style="color:#0099a7">&lt;?php</span> <span style="color:#2a50b7">echo</span> $<span style="color:#2a50b7">src;</span><span style="color:#0099a7"> ?&gt;</span><span style="color:#0099a7">"</span> <span style="color:#f09642">alt</span><span style="color:#0099a7">="</span><span style="color:#545454;">My Image</span><span style="color:#0099a7">"&gt;</span>
					<br>
					<span style="color:#0099a7">&lt;/</span>picture<span style="color:#0099a7">&gt;</span>
				</div>
			</div>
			<div style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;">
				<h3 style="margin:0;padding:0;">Gutenberg method</h3>
				<br>
				Coming soon...
			</div>
			<br>
			<h1><?php _e('Converter', RDEV_WEBP_DOMAIN); ?></h1>
			<span>Attention! If your site has a lot of files, the converter can put a heavy load on the processor for a long time. The conversion time for 50 files is approx. 30 seconds.</span>
			<div id="webp-conversion-start" style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;display: none;">
				<div style="display: flex;align-items: flex-start;">
					<img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4gICAgPGRlZnM+ICAgICAgICA8bGluZWFyR3JhZGllbnQgeDE9IjguMDQyJSIgeTE9IjAlIiB4Mj0iNjUuNjgyJSIgeTI9IjIzLjg2NSUiIGlkPSJhIj4gICAgICAgICAgICA8c3RvcCBzdG9wLWNvbG9yPSIjNDQ0IiBzdG9wLW9wYWNpdHk9IjAiIG9mZnNldD0iMCUiLz4gICAgICAgICAgICA8c3RvcCBzdG9wLWNvbG9yPSIjNDQ0IiBzdG9wLW9wYWNpdHk9Ii42MzEiIG9mZnNldD0iNjMuMTQ2JSIvPiAgICAgICAgICAgIDxzdG9wIHN0b3AtY29sb3I9IiM0NDQiIG9mZnNldD0iMTAwJSIvPiAgICAgICAgPC9saW5lYXJHcmFkaWVudD4gICAgPC9kZWZzPiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMSAxKSI+ICAgICAgICAgICAgPHBhdGggZD0iTTM2IDE4YzAtOS45NC04LjA2LTE4LTE4LTE4IiBpZD0iT3ZhbC0yIiBzdHJva2U9InVybCgjYSkiIHN0cm9rZS13aWR0aD0iMiI+ICAgICAgICAgICAgICAgIDxhbmltYXRlVHJhbnNmb3JtICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iICAgICAgICAgICAgICAgICAgICB0eXBlPSJyb3RhdGUiICAgICAgICAgICAgICAgICAgICBmcm9tPSIwIDE4IDE4IiAgICAgICAgICAgICAgICAgICAgdG89IjM2MCAxOCAxOCIgICAgICAgICAgICAgICAgICAgIGR1cj0iMC45cyIgICAgICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPiAgICAgICAgICAgIDwvcGF0aD4gICAgICAgICAgICA8Y2lyY2xlIGZpbGw9IiM0NDQiIGN4PSIzNiIgY3k9IjE4IiByPSIxIj4gICAgICAgICAgICAgICAgPGFuaW1hdGVUcmFuc2Zvcm0gICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgICAgICAgICAgICAgICAgICAgIHR5cGU9InJvdGF0ZSIgICAgICAgICAgICAgICAgICAgIGZyb209IjAgMTggMTgiICAgICAgICAgICAgICAgICAgICB0bz0iMzYwIDE4IDE4IiAgICAgICAgICAgICAgICAgICAgZHVyPSIwLjlzIiAgICAgICAgICAgICAgICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+ICAgICAgICAgICAgPC9jaXJjbGU+ICAgICAgICA8L2c+ICAgIDwvZz48L3N2Zz4=" alt="Loader">
					<p style="margin-left:15px;"><strong><?php _e('Your images are being converted...', RDEV_WEBP_DOMAIN) ?></strong></p>
				</div>
			</div>
			<div id="webp-conversion-result" style="border: 1px solid #e2e4e7;background-color:#fff;padding: 15px;margin: 15px 0;display: none;">
				<h3 id="webp-conversion-result-title" style="margin:0;padding:0;margin-bottom:5px;">ERROR</h3>
				<span id="webp-conversion-result-description">Unknown error</span>
			</div>
			<div style="margin-top: 15px;">
				<a href="#run-converter" id="webp-run-converter" class="button" style="background-color: #fff;"><?php _e('Click here to add WebP alternatives to all JPEG\'s', RDEV_WEBP_DOMAIN) ?></a>
			</div>
		</div>
	</div>

<?php
	add_action('admin_footer', function(){
?>
<script>
	var rdev_webp = {
		url: "<?php echo admin_url('admin-ajax.php'); ?>",
		nonce: "<?php echo wp_create_nonce('nonce_rdev_webp'); ?>"
	};
	//Isolate script
	(function(){
		'use strict';
		jQuery('#webp-run-converter').on('click', function(e){
			e.preventDefault();
			console.log('WebP: Start WebP converter');
			jQuery(this).attr('disabled', true);
			if(jQuery('#webp-conversion-result').is(':visible'))
			{
				jQuery('#webp-conversion-result').slideToggle();
			}
			if(jQuery('#webp-conversion-start').is(':hidden'))
			{
				jQuery('#webp-conversion-start').slideToggle();
			}
			jQuery.ajax({
				url: rdev_webp.url,
				type:'post',
				data:{
					action: 'webp_force_jpeg',
					salt: rdev_webp.nonce,
					request: 'run',
				},
				success:function(e)
				{
					console.log(e);

					if(/^[\],:{}\s]*$/.test(e.replace(/\\["\\\/bfnrtu]/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))
					{
						var o = JSON.parse(e);
						if(o.hasOwnProperty('status')){
							var response_title = 'Error';
							var response_description = 'Unknown error';
							if(o.status == 1){
								switch(o.response){
									case 'success':
										console.log('WebP: Success!');
										console.log('WebP: Converted images - '+o.c_cov+', Errors - '+o.c_err);
										response_title = '<?php _e('Success', RDEV_WEBP_DOMAIN); ?>';
										response_description = '<?php _e('File conversion completed successfully', RDEV_WEBP_DOMAIN); ?>.<br><strong><?php _e('Converted JPEG', RDEV_WEBP_DOMAIN) ?>: '+o.c_cov+'<br><strong><?php _e('Errors', RDEV_WEBP_DOMAIN); ?>: '+o.c_err;
										break;
									case 'error_1':
										console.log('WebP: Error #1 - Request does not exist.');
										response_title = '<?php _e('Error', RDEV_WEBP_DOMAIN); ?> #1';
										response_description = '<?php _e('The form contains errors', RDEV_WEBP_DOMAIN); ?>';
										break;
									case 'error_2':
										console.log('WebP: Error #2 - Converter locked.');
										response_title = '<?php _e('Error', RDEV_WEBP_DOMAIN); ?> #2';
										response_description = '<?php _e('The converter is currently blocked. Wait a moment...', RDEV_WEBP_DOMAIN); ?>';
										break;
									default:
										console.log('WebP: Unknown error');
										break;
								}

								//Response
								jQuery('#webp-conversion-result-title').html(response_title);
								jQuery('#webp-conversion-result-description').html(response_description);
								if(jQuery('#webp-conversion-start').is(':visible'))
								{
									jQuery('#webp-conversion-start').slideToggle();
								}
								if(jQuery('#webp-conversion-result').is(':hidden'))
								{
									jQuery('#webp-conversion-result').slideToggle();
								}
								jQuery('#webp-run-converter').attr('disabled', false);
							}
						}
					}
				}
			});
		});
	})();
</script>
<?php
	});
?>
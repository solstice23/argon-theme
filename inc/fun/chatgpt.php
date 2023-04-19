<?php

use GuzzleHttp\Exception\GuzzleException;
use HaoZiTeam\ChatGPT\V2 as ChatGPTV2;

function argon_generate_chatgpt_client(): ?ChatGPTV2 {

	$apikey  = get_option( "argon_openai_api_key" );
	$baseurl = $GLOBALS["openai_baseurl"];

	if ( ! $apikey || ! isset( $baseurl ) ) {
		return null;
	}

	return new ChatGPTV2( $apikey, $baseurl , model: get_option('argon_ai_model', 'gpt-3.5-turbo'), timeout: 30 );
}

/**
 * @throws Exception|GuzzleException
 */
function argon_generate_article_summary( int $post_id, WP_Post $post ): string {
	$client = argon_generate_chatgpt_client();

	$client->addMessage(
		"You are an article summary generator, ".
		"please generate the summary of the given article ".
		"with provided title and content, ".
		"the language of the summary must equals to the article's mainly used language, ".
		"do not write summary in third person.".
		"If there's a previous summary of an article provided, ".
		"please generate the summary similar with the previous one.",
		"system" );
	$client->addMessage( "The title of the article：" . $post->post_title );

	$content = $post->post_content;
	$content = wp_strip_all_tags(apply_filters('the_content', $content));
	$max_content_length = get_option( 'argon_ai_max_content_length', 4000 );
	if ($max_content_length > 0){
		$content = substr($content, 0, $max_content_length);
	}

	$client->addMessage( "The content of the article：" . $content );
	$previous_summary = get_post_meta( $post_id, "argon_ai_summary", true );
	if ( $previous_summary != "" ) {
		$client->addMessage( "The previous summary of the article: " . $previous_summary);
	}

	$post_argon_ai_extra_prompt_mode = get_post_meta( $post_id, "argon_ai_extra_prompt_mode", true );
	$post_argon_ai_extra_prompt      = get_post_meta( $post_id, "argon_ai_extra_prompt", true );
	$global_argon_ai_extra_prompt    = get_option( 'argon_ai_extra_prompt', "" );
	switch ( $post_argon_ai_extra_prompt_mode ) {
		case 'replace':
			$client->addMessage( $post_argon_ai_extra_prompt, 'system' );
			break;
		case 'append':
			$client->addMessage( $global_argon_ai_extra_prompt . $post_argon_ai_extra_prompt, 'system' );
			break;
		case 'none':
			break;
		case 'default':
		default:
			$client->addMessage( $global_argon_ai_extra_prompt, 'system' );
			break;
	}

	$result = "";
	foreach ( $client->ask( "Now please generate the summary of the article given before" ) as $item ) {
		$result .= $item['answer'];
	}

	return $result;
}


add_action( "save_post_post", function ( int $post_id, WP_Post $post, bool $update ) {
	if ( get_option( 'argon_ai_post_summary', false ) == 'false' || get_post_meta( $post_id, "argon_ai_post_summary", true ) == 'false' ) {
		return;
	}
	$post_argon_ai_no_update_post_summary   = get_post_meta( $post_id, "argon_ai_no_update_post_summary", true );
	$global_argon_ai_no_update_post_summary = get_option( 'argon_ai_no_update_post_summary', true );
	if ( $update && $post_argon_ai_no_update_post_summary != 'false' && ( $post_argon_ai_no_update_post_summary == 'true' || $global_argon_ai_no_update_post_summary == 'true' ) ) {
		return;
	}
	try {
		$summary = argon_generate_article_summary( $post_id, $post );
		update_post_meta( $post_id, "argon_ai_summary", $summary );
	} catch ( Exception|GuzzleException $e ) {
		error_log( $e );
	}
}, 10, 3 );
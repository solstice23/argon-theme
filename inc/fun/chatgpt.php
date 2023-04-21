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
		$client->addMessage( "The previous summary of the article, please generate the summary similar with the previous one: " . $previous_summary);
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


function argon_on_save_post( int $post_id, WP_Post $post, string $old_status ): void {
//	if ( false !== wp_is_post_autosave( $post_id ) || 'auto-draft' == $post->post_status ) {
//		return;
//	}

	// If this is a revision, get real post ID.
	$revision_id = wp_is_post_revision( $post_id );

	if ( false !== $revision_id ) {
		$post_id = $revision_id;
	}

	if ( get_option( 'argon_ai_post_summary', false ) == 'false' || get_post_meta( $post_id, "argon_ai_post_summary", true ) == 'false' ) {
		return;
	}
	$update                                 = $old_status === $post->post_status;
	$post_argon_ai_no_update_post_summary   = get_post_meta( $post_id, "argon_ai_no_update_post_summary", true );
	$global_argon_ai_no_update_post_summary = get_option( 'argon_ai_no_update_post_summary', true );
	if ( $update && $post_argon_ai_no_update_post_summary != 'false' && ( $post_argon_ai_no_update_post_summary == 'true' || $global_argon_ai_no_update_post_summary == 'true' ) ) {
		return;
	}

	if ( get_option( 'argon_ai_async_generate', true ) != 'false' ) {
		wp_schedule_single_event( time() + 1, 'argon_update_ai_post_meta', array( $post_id ) );
	} else {
		argon_update_ai_post_meta( $post_id );
	}
}

function argon_update_ai_post_meta( $post_id ): void {
	try {
		$summary = argon_generate_article_summary( $post_id, get_post( $post_id ) );
		update_post_meta( $post_id, "argon_ai_summary", $summary );
	} catch ( Exception|GuzzleException $e ) {
		error_log( $e );
	}
}

add_action( "argon_update_ai_post_meta", "argon_update_ai_post_meta" );
add_action( "publish_post", "argon_on_save_post", 20, 3 );
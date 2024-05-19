<?php
/**
 * headless functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package headless
 */
require get_template_directory() . '/messages/index.php';

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

//modify auth response
function modify_token_response($data, $user) {
	$res = array(
    'token' => $data['token'],
		'user' => array(
			'email' => $user->data->user_email,
			'user_display_name' => $user->data->display_name,
			'user_nicename' => $user->data->user_nicename,
			'id' => $user->ID,
			'role' => get_userdata($user->ID)->roles
		)
	);

	return $res;
}
add_filter('jwt_auth_token_before_dispatch', 'modify_token_response', 10, 2);



add_action( 'rest_api_init', function(){
	register_rest_route( 'myplug/v2', '/users/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => 'get_works_by_user',
	) );

	  register_rest_field('user', 'roles', array(
			'get_callback' => 'get_user_roles',
			'update_callback' => null,
			'schema' => array(
				'type' => 'array'
			)
		));
} );

function get_works_by_user(WP_REST_Request $request){
	$items = array();
	if (get_field('works', 'user_'.$request['id'])) {
		$posts = get_posts( array (
			'post_status' => 'publish',
			'numberposts' => -1,
			'include' => get_field('works', 'user_'.$request['id']),
			'post_type' => 'works',
			'meta_key' => 'date',
			'orderby' => 'meta_value',
      'order' => 'DESC',
			'meta_query' => [ [
				'key' => 'state',
				'value' => $request['state'],
			] ],
		) ) ;

		foreach( $posts as $post ){
			$field = get_field('foreman_info', $post->ID);
			$arr = $field['workers'];
			$workers = array_column($arr, 'worker');
			$found_key = array_search($request['id'], array_column($workers , 'ID'));
			$user = $arr[$found_key];
			$items[] = array(
				'id'      => $post->ID,
				'created_date' => $post->post_date,
				'date' => get_field('date', $post->ID),
				'time' => get_field('customer_info', $post->ID)['time'],
				'status' => $field['status'],
				'state' => get_field('state', $post->ID),
				'workers_count' => $field['workers_count'],
				'total_time' => $field['total_time'],
				'workers' => $arr,
				'worker' => $user,
				'tips' => $field['tips']
			);
		}
	}

	$user = array(
		'name' => get_author_name((int) $request['id']),
		'works' => $items
	);

	return $user;
}

function get_user_roles($object, $field_name, $request) {
  return get_userdata($object['id'])->roles;
}

add_filter( 'rest_works_query', function( $args ) {
  $ignore = array('page', 'per_page', 'search', 'order', 'orderby', 'slug', '_fields', 'author', 'startd', 'endd', 'sortbydate', 'notpending');

  foreach ( $_GET as $key => $value ) {
    if (!in_array($key, $ignore)) {
      $args['meta_query'][] = array(
        'key'   => $key,
        'value' => $value,
      );
    }
  }
	if(isset($_GET['startd'])) {
		$args['meta_query'][] = array(
			'key'   => 'date',
			'value' => array( $_GET['startd'], $_GET['endd']),
			'type' => 'DATE',
			'compare' => 'BETWEEN'
		);
	}

		if(isset($_GET['sortbydate'])) {
			$args['meta_key'] = 'date';
			$args['orderby'] = 'meta_value';
		}

		if(isset($_GET['notpending'])) {
			$args['meta_query'][] = array(
        'key'   => 'state',
        'value' => 'pending',
				'compare' => '!='
      );
		}

  return $args;
});



function works_created_notification( $post ) {
	restSendEmail($post);
}
add_action( 'rest_after_insert_works', 'works_created_notification', 100, 3);


function react_sender_cron_mail() {
	sendReminder();

}
add_action( 'react_sender_cron', 'react_sender_cron_mail' );

function maximum_api_filter($query_params) {
    $query_params['per_page']["maximum"]=200;
    return $query_params;
}
add_filter('rest_works_collection_params', 'maximum_api_filter');
<?php

function workerConfirmedJobSms($post, $client, $twilio_number) {
  $date = get_field('date', $post);

  $fields = get_field('foreman_info', $post->ID);
  $arr = $fields['workers'];
  $found_key = array_search('foreman', array_column($arr, 'worker_role'));
  $user_id = $arr[$found_key]['worker'];
  $user = get_userdata($user_id);
  $user_name = $user->display_name;

  $message = "{$user_name} confirmed job #{$post->ID} at {$date}";
  //sms
  $phone = "5105667471";
  $client->messages->create(
      $phone,
      array(
          'from' => $twilio_number,
          'body' => $message
      )
  );

}

<?php
require get_template_directory() . '/messages/moveConfirmation.php';
require get_template_directory() . '/messages/moveCompleted.php';
require get_template_directory() . '/messages/moveConfirmed.php';
require get_template_directory() . '/messages/assignWorkers.php';
require get_template_directory() . '/messages/workerConfirmedJob.php';
require get_template_directory() . '/twillio/src/Twilio/autoload.php';
use Twilio\Rest\Client;

function restSendEmail($post) {
  $account_sid = TWILLIO_ACCOUNT_SID;
  $auth_token = TWILLIO_AUTH_TOKEN;
  $twilio_number = TWILLIO_PHONE;
  $client = new Client($account_sid, $auth_token);
  $state = get_field('state', $post);
  $foreman_info = get_field('foreman_info', $post);
  if ($state === "pending") {
    moveConfirmationEmail($post);
    moveConfirmationSms($post, $client, $twilio_number);
  }
  if ($state === "completed") {
    moveCompletedEmail($post);
  }
  if ($state === "confirmed" && empty($foreman_info['truck'])) {
    moveConfirmedEmail($post);
    moveConfirmedSms($post, $client, $twilio_number);
  }
  if ($state === "assignWorkers" && $foreman_info['status'] === "pending") {
    assignWorkersSms($post, $client, $twilio_number);
  }
  if ($state === "assignWorkers" && $foreman_info['status'] === "confirmed") {
    workerConfirmedJobSms($post, $client, $twilio_number);
  }
}

function sendReminder() {
  $account_sid = TWILLIO_ACCOUNT_SID;
  $auth_token = TWILLIO_AUTH_TOKEN;
  $twilio_number = TWILLIO_PHONE;
  $client = new Client($account_sid, $auth_token);
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $subject = "Reminder for your upcoming move";
  $args = array(
    'post_type' => 'works',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'key'     => 'date',
        'value'   => date('Y-m-d'),
        'compare' => '=',
        'type' => 'DATE'
      ),
    ),
  );

  $query = new WP_Query( $args );
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      $date = get_field('date');
      $customer_info = get_field('customer_info');
      $time = $customer_info['time'];
      $email = $customer_info['customer_email'];
      $phone = $customer_info['customer_phone'];
      $messageEmail = "
      <html>
      <body>
        <p>Hi, Your move is scheduled for the date / time listed below.</p>
        <p>{$date} / ${time}</p>
        <p>If you need to reschedule or have any other questions please contact us at <a href='tel:(510) 566-7471'>(510) 566-7471</a></p>
        <p>Thank you!</p>
        <p>The Smart People Moving Team</p>
      </body>
      </html>
      ";
      $messageSms = "Hi, Your move is scheduled for the date / time listed below.
{$date} / ${time}
If you need to reschedule or have any other questions please contact us at (510) 566-7471
Thank you!
The Smart People Moving Team";

      wp_mail( $email, $subject, $messageEmail, $headers);

      $client->messages->create(
          $phone,
          array(
              'from' => $twilio_number,
              'body' => $messageSms
          )
      );
    }
  }
  wp_reset_postdata();
}

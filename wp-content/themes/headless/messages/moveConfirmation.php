<?php
function moveConfirmationEmail($post) {
  $customer_info = get_field('customer_info', $post);
  $name = $customer_info['customer_name'];
  $email = $customer_info['customer_email'];
  $link = 'https://smartpeoplemoving.com/book.html';
  $subject = "Move confirmation | Smart People Moving";
  $message = "
  <html>
  <body>
    <p>
      Hello {$name}! Thank you for choosing Smart People Moving! To confirm your booking request please fill out the form by clicking the link below.
    </p>
    <a href='{$link}?work={$post->ID}'>Confirm Move</a>
    <p>
      If you have any questions or concerns please contact us at <a href='tel:(510) 566-7471'>(510) 566-7471</a>
    </p>
    <p>
      The Smart People Moving Team
    </p>
  </body>
  </html>
  ";

  //email
  $to[] = sprintf( '%s <%s>', $name, $email );
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  wp_mail( $to, $subject, $message, $headers );
}

function moveConfirmationSms($post, $client, $twilio_number) {
  $customer_info = get_field('customer_info', $post);
  $name = $customer_info['customer_name'];
  $phone = $customer_info['customer_phone'];
  $link = 'https://smartpeoplemoving.com/book.html';
  $message = "
      Hello {$name}! Thank you for choosing Smart People Moving! To confirm your booking request please fill out the form by clicking the link below. \n{$link}?work={$post->ID} \nIf you have any questions or concerns please contact us at (510) 566-7471 \nThe Smart People Moving Team
  ";

  //sms

  $client->messages->create(
      $phone,
      array(
          'from' => $twilio_number,
          'body' => $message
      )
  );
}

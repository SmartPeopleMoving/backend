<?php


//Total?
function moveCompletedEmail($post) {
  $customer_info = get_field('customer_info', $post);
  $name = $customer_info['customer_name'];
  $email = $customer_info['customer_email'];
  $subject = "Move Completed | Smart People Moving";
  $message = "
  <html>
  <body>
    <p>
      Thank you for choosing Smart People Moving! Your Move Was Completed.
    </p>
    <p>
      If you have any questions or concerns please contact us at <a href='tel:(510) 566-7471'>(510) 566-7471</a> or send an email to <a href='mailto:smart.people.move@gmail.com'>smart.people.move@gmail.com</a>
    </p>
    <p>
      The Smart People Moving Team
    </p>

  ";
  $to[] = sprintf( '%s <%s>', $name, $email );
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  wp_mail( $to, $subject, $message, $headers );
}

<?php
function moveConfirmedEmail($post) {
  $field = get_field('customer_info', $post);
  $name = $field['customer_name'];
  $email = $field['customer_email'];
  $phone = $field['customer_phone'];
  $date = get_field('date', $post);
  $contactName = $field['contact_name'];
  $contactPhone = $field['contact_phone'];
  $contactEmail = $field['contact_email'];
	$resultNumber = floatval($field['result']);
  $cash = 0;
  $credit = 0;
  if($field['payment'] === 'cash') {
    $cash = $resultNumber;
    $credit = $resultNumber + 10;
  } else {
    $cash = $resultNumber - 10;
    $credit = $resultNumber;
  }
  $time = '';
  switch ($field['time']) {
    case '08:00':
      $time = '8-8.30am';
      break;
    case '09:00':
      $time = '9-9.30am';
      break;
    case '10:00':
      $time = '10-10.30am';
      break;
    case '11:00':
      $time = '11-11.30am';
      break;
    case '12:00':
      $time = '12-12.30pm';
      break;
    case '14:00':
      $time = '2-4pm';
      break;
    default:
      $time = '';
      break;
  }

  $heavyItemsPrice = '';
  if($field['heavyItems'] !== 'No') {
    $heavyItemsPrice = '($250)';
  }

  if ($contactName && $contactPhone && $contactEmail) {
      $contactInfo = "
          Contact name: {$contactName}<br />
          Contact Phone: <a href='tel:{$contactPhone}'>{$contactPhone}</a><br />
          Contact Email: <a href='mailto:{$contactEmail}'>{$contactEmail}</a><br />
      ";

  } else {
    $contactInfo = "";
  }

  $pickup_address = '';
  if($field['pickup_address']) {
    for($i = 0; $i < count($field['pickup_address']); $i++) {
        $address = $field['pickup_address'][$i]["full_address"];
        $unit = $field['pickup_address'][$i]["unit"];
        $pickup_address .= "
            From: <a rel='noreferrer' target='_blank' href='https://maps.google.com/?q={$address}'>{$address}</a>, Unit: {$unit}<br />
        ";
    }
  }

  $dropoff_address = '';
  if ($field['dropoff_address']) {
    for($i = 0; $i < count($field['dropoff_address']); $i++) {
      $address2 = $field['dropoff_address'][$i]["full_address"];
      $unit2 = $field['dropoff_address'][$i]["unit"];
        $dropoff_address .= "
            To: <a rel='noreferrer' target='_blank' href='https://maps.google.com/?q={$address2}'>{$address2}</a>, Unit: {$unit2}<br />
        ";
    }
  }

  $supplies = '';
  if ($field['supplies'] === 'yes') {
    if ($field['small_boxes']) {
      $supplies .= "Small Box: {$field['small_boxes']}<br />";
    }
    if ($field['medium_boxes']) {
      $supplies .= "Small Box: {$field['medium_boxes']}<br />";
    }
    if ($field['wrapping_paper']) {
      $supplies .= "Small Box: {$field['wrapping_paper']}<br />";
    }
  }


  $message = "
  <html>
    <body>
      Request: #{$post->ID}<br />
      Status: Confirmed<br />
      Move Date: {$date}<br />
      Start Time: {$time}<br />
      Crew Size: {$field['movers']}<br />
      Hourly rate: \${$cash} (cash)/ \${$credit} (card payment)<br />
      Payment: {$field['payment']}<br />
      Arriving fee: {$field['truck_fee']}<br />
      Size of move: {$field['bedroom']}<br />
      Type of residency: {$field['typeofresidency']}<br />
      Truck: {$field['truck']}<br />
      Heavy items: {$field['heavyItems']}{$heavyItemsPrice}<br />
      <br />
      {$pickup_address}
      {$dropoff_address}
      <br />
      {$contactInfo}
      <br />
      Packing: {$field['packing']}<br />
      <br />
      {$supplies}
      <br />
      Customer name: {$field['customer_name']}<br />
      Customer Phone: <a href='tel:{$phone}'>{$phone}</a><br />
      Customer Email: <a href='mailto:{$email}'>{$email}</a><br />
      From: {$field['howfrom']}<br />
      Additional information: {$field['note']}
    </body>
  </html>
  ";

  $clientMessage = "
  <html>
    <body>
      <p>Thank you for choosing Smart People Moving!!</p>
      Request: #{$post->ID}<br />
      Status: Confirmed<br />
      Move Date: {$date}<br />
      Start Time: {$time}<br />
      Crew Size: {$field['movers']}<br />
      Hourly rate: \${$cash} (cash)/ \${$credit} (card payment)<br />
      Payment: {$field['payment']}<br />
      Arriving fee: {$field['truck_fee']}<br />
      Size of move: {$field['bedroom']}<br />
      Type of residency: {$field['typeofresidency']}<br />
      Truck: {$field['truck']}<br />
      Heavy items: {$field['heavyItems']}{$heavyItemsPrice}<br />
      <br />
      {$pickup_address}
      {$dropoff_address}
      <br />
      {$contactInfo}
      <br />
      Packing: {$field['packing']}<br />
      <br />
      {$supplies}
      <br />
      Customer name: {$field['customer_name']}<br />
      Customer Phone: <a href='tel:{$phone}'>{$phone}</a><br />
      Customer Email: <a href='mailto:{$email}'>{$email}</a><br />
      From: {$field['howfrom']}<br />
      Additional information: {$field['note']}<br />
      <div>
      <b>Attantion: </b>
      <ul>
        <li>3 hours minimum mandatory</li>
        <li>If the distance between pick up and drop off adress  more than 10 miles, driving time would be doubled!</li>
        <li>Total cost = hourly rate +arriving fee+driving time</li>
      </ul>
      </div>
      <div>
        <b>Attantion: </b>
        <p>The customer must confirm or cancel the order minimum 48 hours before the date specified in the request!</p>
      </div>
    </body>
  </html>
  ";
  $subject = sprintf( 'Order #%s | Smart People Moving', $post->ID );
  $to[] = sprintf( '%s <%s>', $name, $email );
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  wp_mail( $to, $subject, $clientMessage, $headers );
  wp_mail( 'smart.people.move@gmail.com', $subject, $message, $headers );
  //wp_mail( 'maxbuldov@gmail.com', $subject, $message, $headers );
}

function moveConfirmedSms($post, $client, $twilio_number) {
  $field = get_field('customer_info', $post);
  $name = $field['customer_name'];
  $email = $field['customer_email'];
  $phone = $field['customer_phone'];
  $date = get_field('date', $post);
  $contactName = $field['contact_name'];
  $contactPhone = $field['contact_phone'];
  $contactEmail = $field['contact_email'];
	$resultNumber = floatval($field['result']);
  $cash = 0;
  $credit = 0;
  if($field['payment'] === 'cash') {
    $cash = $resultNumber;
    $credit = $resultNumber + 10;
  } else {
    $cash = $resultNumber - 10;
    $credit = $resultNumber;
  }

  $time = '';
  switch ($field['time']) {
    case '09:00':
      $time = '9am';
      break;
    case '10:00':
      $time = '10am';
      break;
    case '11:00':
      $time = '11am';
      break;
    case '12:00':
      $time = '12pm';
      break;
    case '14:00':
      $time = '2-4pm';
      break;
    default:
      $time = '';
      break;
  }
  $heavyItemsPrice = '';
  if($field['heavyItems'] !== 'No') {
    $heavyItemsPrice = '($250)';
  }
  if ($contactName && $contactPhone && $contactEmail) {
      $contactInfo = "Contact name: {$contactName}\nContact Phone: {$contactPhone}\nContact Email: {$contactEmail}";

  } else {
    $contactInfo = "";
  }

  $pickup_address = '';
  if($field['pickup_address']) {
    for($i = 0; $i < count($field['pickup_address']); $i++) {
        $address = $field['pickup_address'][$i]["full_address"];
        $unit = $field['pickup_address'][$i]["unit"];
        $pickup_address .= "From: {$address}, Unit: {$unit}\n";
    }
  }

  $dropoff_address = '';
  if ($field['dropoff_address']) {
    for($i = 0; $i < count($field['dropoff_address']); $i++) {
      $address2 = $field['dropoff_address'][$i]["full_address"];
      $unit2 = $field['dropoff_address'][$i]["unit"];
        $dropoff_address .= "To: {$address2}, Unit: {$unit2}\n";
    }
  }

  $supplies = '';
  if ($field['supplies'] === 'yes') {
    if ($field['small_boxes']) {
      $supplies .= "Small Box: {$field['small_boxes']}";
    }
    if ($field['medium_boxes']) {
      $supplies .= "Small Box: {$field['medium_boxes']}";
    }
    if ($field['wrapping_paper']) {
      $supplies .= "Small Box: {$field['wrapping_paper']}";
    }
  }


  $message = "
  Request: #{$post->ID}\n
  Status: Confirmed\n
  Move Date: {$date}\n
  Start Time: {$time}\n
  Crew Size: {$field['movers']}\n
  Hourly rate: \${$cash} (cash)/ \${$credit} (card payment)\n
  Payment: {$field['payment']}\n
  Arriving fee: {$field['truck_fee']}\n
  Size of move: {$field['bedroom']}\n
  Type of residency: {$field['typeofresidency']}\n
  Truck: {$field['truck']}\n
  Heavy items: {$field['heavyItems']}{$heavyItemsPrice}\n
  \n
  {$pickup_address}
  {$dropoff_address}
  \n
  {$contactInfo}
  \n
  Packing: {$field['packing']}\n
  \n
  {$supplies}
  \n
  Customer name: {$field['customer_name']}\n
  Customer Phone: <a href='tel:{$phone}'>{$phone}</a>\n
  Customer Email: <a href='mailto:{$email}'>{$email}</a>\n
  From: {$field['howfrom']}\n
  Additional information: {$field['note']}
  ";

  $clientMessage = "
Thank you for choosing Smart People Moving!
Request: #{$post->ID}
Status: Confirmed
Move Date: {$date}
Start Time: {$time}
Crew Size: {$field['movers']}
Hourly rate: \${$cash} (cash)/ \${$credit} (card payment)
Payment: {$field['payment']}
Arriving fee: {$field['truck_fee']}
Size of move: {$field['bedroom']}
Type of residency: {$field['typeofresidency']}
Truck: {$field['truck']}
Heavy items: {$field['heavyItems']}{$heavyItemsPrice}
{$pickup_address}
{$dropoff_address}
{$contactInfo}
Packing: {$field['packing']}
{$supplies}
Customer name: {$field['customer_name']}
Customer Phone: {$phone}
Customer Email: {$email}
From: {$field['howfrom']}
Additional information: {$field['note']}
Attantion:
3 hours minimum mandatory
If the distance between pick up and drop off adress  more than 10 miles, driving time would be doubled!
Total cost = hourly rate + arriving fee+driving time
Attantion:
The customer must confirm or cancel the order minimum 48 hours before the date specified in the request!
  ";
  //sms
  $client->messages->create(
      $phone,
      array(
          'from' => $twilio_number,
          'body' => $clientMessage
      )
  );
}

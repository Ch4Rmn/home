<?php
// echo date_default_timezone_set('Asia,Rangoon');
// echo date('h:m A');
$timeZone = 'Asia/Rangoon';
$dateTime = new DateTime('now', new DateTimeZone($timeZone));
echo $dateTime->format('Y-m-d H:i a');


// Assuming $created_at is the datetime string in UTC or another time zone
$created_at = '2024-03-31 12:00:00'; // Example datetime string

// Create a DateTime object from the $created_at datetime string
$dateTime = new DateTime($created_at, new DateTimeZone('UTC'));

// Set the desired time zone
$timeZone = 'Asia/Rangoon';
$dateTime->setTimezone(new DateTimeZone($timeZone));

// Format the datetime in the specified time zone
echo $dateTime->format('Y-m-d H:i a');
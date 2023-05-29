<?php

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require "PHPMailer/src/Exception.php";
  require "PHPMailer/src/PHPMailer.php";

  $mail = new PHPMailer(true);

  $mail->CharSet = "UTF-8";
  $mail->IsHTML(true);

  $name = $_POST["name"];
  $time = $_POST["time"];
  $phone = $_POST["phone"];
  $message = $_POST["message"];
  $time_template = "template_mail.html";

  $body = file_get_contents($time_template);
	$body = str_replace('%name%', $name, $body);
	$body = str_replace('%time%', $time, $body);
	$body = str_replace('%phone%', $phone, $body);
	$body = str_replace('%message%', $message, $body);

  $mail->addAddress("kamil@citynix.ru");   // Здесь введите Email, куда отправлять
	$mail->setFrom($time);
  $mail->Subject = "[Заявка с формы]";
  $mail->MsgHTML($body);

  if (!$mail->send()) {
    $message = "Ошибка отправки";
  } else {
    $message = "Данные отправлены!";
  }

	$response = ["message" => $message];

  header('Content-type: application/json');
  echo json_encode($response);


?>

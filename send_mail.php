<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  require "PHPMailer/src/Exception.php";
  require "PHPMailer/src/PHPMailer.php";
  $mail = new PHPMailer(true);
  $mail->CharSet = "UTF-8";
  $mail->IsHTML(true);

  $name = $_POST["name"];
  $phone = $_POST["phone"];
  $time = $_POST["time"];
  $email_template = "template_mail.html";
  $body = file_get_contents($email_template);
	$body = str_replace('%name%', $name, $body);
	$body = str_replace('%phone%', $phone, $body);
	$body = str_replace('%time%', $time, $body);

  $mail->addAddress("kamil@citynix.ru");   // Здесь введите Email, куда отправлять
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

<!DOCTYPE HTML>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title>E-mail</title>
</head>

<body>
<?php
include("core.php");

      $nome =$_POST['nome'];
	  $cidade=$_POST['cidade'];
      $email=$_POST['email'];
	  $mens=$_POST['mens'];

      if(empty($email))
      {
        $alert = "<div id=\"alert\" class=\"erro\"><b>Preencha o campo com seu email <b></div>";
      }
      

      else
      {
        require('PHPMailer/class.phpmailer.php');

       $mail = new PHPMailer();
       $mail->IsSMTP();
	   $mail->Host = 'smtp.googlemail.com';
       $mail->SMTPAuth = true;
       $mail->Port = 587;
	   $mail->SMTPSecure = 'tls';
       $mail->Username = 'noreplay.talentodesign@gmail.com';
       $mail->Password = 'designer2802';
       $mail->SetFrom('noreply.talentodesign@gmail.com', 'Contato via Site');
       $mail->AddAddress($core_email, 'Contato'.$core_nome);
       $mail->Subject = 'Mensagem Via Contato do Site';

       $body = "
<meta charset='UTF-8'>
           <strong>Nome      : </strong>{$nome} <br />
           <strong>E-mail    : </strong>{$email} <br />
		   <strong>Cidade    : </strong>{$cidade} <br />
           <strong>Mensagem  : </strong>{$mens}";


       $mail->MsgHTML($body);


        $mensagem = "";


       if($mail->Send())
		   echo "Email enviado";

        else
            echo "Falha no Envio";


      }

?>

</body>

</html>


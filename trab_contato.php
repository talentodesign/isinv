<meta charset="UTF-8">	

<?php
      $nome     = strip_tags(trim($_POST['nome']));
      $email    = strip_tags(trim($_POST['email'])); 
      $arquivo  = $_FILES['anexo'];
      
      $tamanho = 16777216;
      //$tipos   = array('.pdf', '.doc');

      if(empty($nome))
      {
        $alert = "<div id=\"alert\" class=\"erro\">Preencha o campo Nome</div>";
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
       $mail->AddAddress("contato@isinvestimentos.com.br", 'Contato IS Investimentos');
       $mail->Subject = 'Formulario de Contato';
       
       $body = "
			<meta charset='UTF-8'>	
            <strong>Nome         : </strong>{$nome} <br />
			<strong>E-mail       : </strong>{$email} <br />         
            <strong>Arquivo      :</strong> ".$arquivo['name'];
       		
            
       
       $mail->MsgHTML($body);
	   $mail->AddAttachment($arquivo['tmp_name'], $arquivo['name']);
       
       
       if($mail->Send()){
         $enviado=true;
	   }
        else{
			echo "Error sending: " . $mail->ErrorInfo;;
			}
            

        
      }

?>

</body>

</html>


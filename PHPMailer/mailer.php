<?php



require "PHPMailer/PHPMailerAutoload.php";

function smtpmailer($to, $from, $from_name, $subject, $body)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; 
 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;  
        $mail->Username = 'servicecapteurs@gmail.com';
        $mail->Password = 'snas mspv jumj olbc';   
   
   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);
   
        $mail->IsHTML(true);
        $mail->From="servicecapteurs@gmail.com";
        $mail->FromName=$from_name;
        $mail->Sender=$from;
        $mail->AddReplyTo($from, $from_name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        if(!$mail->Send())
        {
            $error ="Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer.";
            return $error; 
        }
        else 
        {
            $error = "Un email de confirmation vous a été envoyé. Veuillez vérifier votre boîte mail.";  
            return $error;
        }
    }

    
?>

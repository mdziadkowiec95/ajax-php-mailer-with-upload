<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * This uses traditional id & password authentication - look at the gmail_xoauth.phps
 * example to see how to use XOAUTH2.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (isset($_POST['fullName']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['damage'])) {
    // form field values
    $full_name = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $damage = $_POST['damage'];

    $errors = array();

    if (empty($full_name)) {
        array_push($errors, 'Podaj imię i nazwisko');
    }

    if (empty($email)) {
        array_push($errors, 'Podaj swój email');
    }

    if (empty($phone)) {
        array_push($errors, 'Podaj numer kontaktowy');
    }

    if (empty($damage)) {
        array_push($errors, 'Musisz wybrać stan rodzaj szkody');
    }

    if (count($errors) > 0) {
        echo json_encode($errors);
    } else {

        require 'vendor/autoload.php';
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->SMTPDebug = 0; // 2 = client and server messages
        $mail->Host = 'smtp.gmail.com'; // Set the hostname of the mail server
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = "hrmdrum@gmail.com";
        $mail->Password = "bronboze";
        $mail->setFrom('test@test.pl', 'Test phpMailer with file');
        $mail->addAddress('hrmdrum@gmail.com', 'HrmDRUM');

        $mail->isHTML(true);  
        $mail->Subject = 'PHPMailer file sender';
        $mail->Body = '
            <h2>Zgłoszenie ze z phpMailer</h2><br>
            <p><strong>Imię i nazwisko:</strong></p>
            <p>' . $full_name . '</p>
            <p><strong>Adres e-mail:</strong></p>
            <p>' . $email . '</p>
            <p><strong>Numer kontaktowy:</strong></p>
            <p>' . $phone . '</p>
            <p><strong>Rodzaj uszkodzenia:</strong></p>
            <p>' . $damage . '</p>';


        $msg = '';
        if (array_key_exists('userFile', $_FILES)) {
            // First handle the upload
            // Don't trust provided filename - same goes for MIME types
            // See http://php.net/manual/en/features.file-upload.php#114004 for more thorough upload validation
            $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['userFile']['name']));
            
            if (move_uploaded_file($_FILES['userFile']['tmp_name'], $uploadfile)) {
                // Upload handled successfully
                $filename = $_FILES["userFile"]["name"];
                $mail->addAttachment($uploadfile, $filename);
        
            } else {
                echo json_encode(array('error' => 'Problem z załadowanie pliku'));
            }
        }

        if (!$mail->send()) {
            $msg .= "Wystąpił błąd: " . $mail->ErrorInfo;
            echo json_encode(array("success" => false, 'msg' => $msg));
        
        } else {
            $msg .= "Zgłoszenie zostało wysłane!";
            echo json_encode(array("success" => true, 'msg' => $msg));
        }
       
    }

} 






//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
// function save_mail($mail)
// {
//     //You can change 'Sent Mail' to any other folder or tag
//     $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
//     //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
//     $imapStream = imap_open($path, $mail->Username, $mail->Password);
//     $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
//     imap_close($imapStream);
//     return $result;
// }
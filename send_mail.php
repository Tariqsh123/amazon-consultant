<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "info@gulfpublisher.com"; // receiver email
    $subject = "New Contact Form Submission - Gulf Publisher";

    // Collect form data
    $name       = htmlspecialchars($_POST['name']);
    $phone      = htmlspecialchars($_POST['phone']);
    $email      = htmlspecialchars($_POST['email']);
    $genre      = htmlspecialchars($_POST['genre']);
    $book_title = htmlspecialchars($_POST['book_title']);
    $details    = htmlspecialchars($_POST['details']);

    // Email content
    $message = "
    New Contact Form Submission:

    Name: $name
    Phone: $phone
    Email: $email
    Genre: $genre
    Book Title: $book_title

    Details:
    $details
    ";

    // Email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Handle file upload (if provided)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        
        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));
        
        $boundary = md5("random"); 
        
        // Headers for attachment
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";
        
        // Message
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message));
        
        // Attachment
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
        $body .= $encoded_content; 

        $sentMail = mail($to, $subject, $body, $headers);
    } else {
        // Send without attachment
        $sentMail = mail($to, $subject, $message, $headers);
    }

    // Redirect or show message
    if ($sentMail) {
        echo "<script>alert('Thank you! Your form has been submitted successfully.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Sorry! Email could not be sent.'); window.history.back();</script>";
    }
}
?>

<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo "Envio não permitido.";
    exit;
}

function clean_text($value) {
    return trim(str_replace(array("\r", "\n"), " ", strip_tags($value ?? "")));
}

$name = clean_text($_POST["name"] ?? "");
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$phone = clean_text($_POST["phone"] ?? "");
$subject = clean_text($_POST["subject"] ?? "Solicitação de orçamento");
$comment = trim(strip_tags($_POST["comment"] ?? ""));

if ($name === "" || $comment === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Preencha nome, email e mensagem antes de enviar.";
    exit;
}

$recipient = "comercial@eletrocomrj.com.br";
$mail_subject = "Contato pelo site: " . ($subject !== "" ? $subject : "Solicitação de orçamento");

$email_content = "Nome: {$name}\n";
$email_content .= "Email: {$email}\n";
$email_content .= "Telefone: {$phone}\n";
$email_content .= "Assunto: {$subject}\n\n";
$email_content .= "Mensagem:\n{$comment}\n";

$email_headers = "From: Eletrocom Site <{$recipient}>\r\n";
$email_headers .= "Reply-To: {$name} <{$email}>\r\n";
$email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($recipient, $mail_subject, $email_content, $email_headers)) {
    http_response_code(200);
    echo "Mensagem enviada com sucesso. Entraremos em contato em breve.";
} else {
    http_response_code(500);
    echo "Não foi possível enviar a mensagem agora. Tente novamente pelo WhatsApp.";
}

?>

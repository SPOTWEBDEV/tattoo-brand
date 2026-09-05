<?php
require __DIR__ . '/config.php';
require __DIR__ . '/email_template.php';
corsHeaders();
requireAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = readJsonBody();
$ref = sanitize($input['ref'] ?? '');
$replyBody = trim((string)($input['reply'] ?? ''));

if ($ref === '' || $replyBody === ''){
    jsonResponse(['error' => 'Missing ref or reply text'], 400);
}

try {
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM messages WHERE msg_ref = :ref LIMIT 1');
    $stmt->execute([':ref' => $ref]);
    $msg = $stmt->fetch();

    if (!$msg){
        jsonResponse(['error' => 'Message not found'], 404);
    }

    $htmlBody  = renderReplyEmail($msg['name'], $msg['message'], $replyBody, $msg['placement']);
    $plainBody = renderReplyEmailPlainText($msg['name'], $msg['message'], $replyBody, $msg['placement']);

    $to      = $msg['email'];
    $subject = 'Re: Your inquiry to ' . STUDIO_NAME;

    // Multipart/alternative message built by hand — plain PHP mail(),
    // no external mailer library. A random boundary string separates
    // the plain-text and HTML parts.
    $boundary = 'bss-' . bin2hex(random_bytes(12));

    $headers  = 'From: ' . STUDIO_NAME . ' <' . STUDIO_EMAIL . ">\r\n";
    $headers .= 'Reply-To: ' . STUDIO_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . "\"\r\n";

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $plainBody . "\r\n\r\n";

    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $htmlBody . "\r\n\r\n";

    $body .= "--{$boundary}--";

    $sent = mail($to, $subject, $body, $headers);

    if (!$sent){
        jsonResponse(['error' => "Email failed to send — check your server's mail configuration (sendmail/SMTP)."], 500);
    }

    $update = $db->prepare('UPDATE messages SET is_read = 1, replied_at = NOW() WHERE msg_ref = :ref');
    $update->execute([':ref' => $ref]);

    jsonResponse(['success' => true]);
} catch (Exception $e){
    error_log('reply_message failed: ' . $e->getMessage());
    jsonResponse(['error' => 'Could not send reply'], 500);
}

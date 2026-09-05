<?php
/**
 * Branded reply-email template.
 * Table-based layout with inline styles throughout — this is the
 * standard approach for HTML email, since many mail clients (Outlook
 * especially) strip <style> blocks and don't support modern CSS.
 */

function renderReplyEmail($customerName, $originalMessage, $replyBody, $placement = ''){
    $accent      = '#d81e3f';
    $ink         = '#1a1a1d';
    $muted       = '#6b6570';
    $border      = '#e7e2e4';
    $panelBg     = '#f7f4f5';

    $safeName     = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
    $safeOriginal = nl2br(htmlspecialchars($originalMessage, ENT_QUOTES, 'UTF-8'));
    $safeReply    = nl2br(htmlspecialchars($replyBody, ENT_QUOTES, 'UTF-8'));
    $safePlacement = htmlspecialchars($placement, ENT_QUOTES, 'UTF-8');
    $studioName   = htmlspecialchars(STUDIO_NAME, ENT_QUOTES, 'UTF-8');
    $studioAddress = htmlspecialchars(STUDIO_ADDRESS, ENT_QUOTES, 'UTF-8');

    $placementRow = $safePlacement !== ''
        ? '<p style="margin:0 0 12px 0; font-size:13px; color:' . $muted . ';">Placement concept: <strong style="color:' . $ink . ';">' . $safePlacement . '</strong></p>'
        : '';

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>A reply from {$studioName}</title>
</head>
<body style="margin:0; padding:0; background:#ececec; font-family:'Segoe UI', Helvetica, Arial, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ececec; padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid {$border};">

          <!-- Header -->
          <tr>
            <td style="background:{$ink}; padding:28px 32px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="vertical-align:middle;">
                    <span style="display:inline-block; width:32px; height:32px; line-height:32px; text-align:center; border:1px solid #45414a; border-radius:50%; color:#ffffff; font-weight:600; font-size:14px; margin-right:10px;">B</span>
                    <span style="color:#ffffff; font-size:17px; font-weight:600; vertical-align:middle;">{$studioName}</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Accent strip -->
          <tr><td style="height:4px; background:{$accent}; line-height:0; font-size:0;">&nbsp;</td></tr>

          <!-- Body -->
          <tr>
            <td style="padding:32px;">
              <p style="margin:0 0 4px 0; font-size:13px; color:{$muted}; text-transform:uppercase; letter-spacing:.04em;">Reply to your inquiry</p>
              <h1 style="margin:0 0 20px 0; font-size:20px; color:{$ink};">Hi {$safeName},</h1>

              {$placementRow}

              <div style="font-size:15px; line-height:1.6; color:{$ink}; margin-bottom:28px;">
                {$safeReply}
              </div>

              <!-- Original message, quoted -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{$panelBg}; border-left:3px solid {$accent}; border-radius:4px;">
                <tr>
                  <td style="padding:16px 18px;">
                    <p style="margin:0 0 6px 0; font-size:12px; font-weight:600; color:{$muted}; text-transform:uppercase; letter-spacing:.04em;">Your original message</p>
                    <p style="margin:0; font-size:14px; line-height:1.6; color:{$muted};">{$safeOriginal}</p>
                  </td>
                </tr>
              </table>

              <p style="margin:28px 0 0 0; font-size:14px; color:{$muted};">
                Reply directly to this email if you have any follow-up questions — it'll come straight back to us.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:24px 32px; background:{$panelBg}; border-top:1px solid {$border};">
              <p style="margin:0 0 4px 0; font-size:13px; color:{$ink}; font-weight:600;">{$studioName}</p>
              <p style="margin:0; font-size:12px; color:{$muted};">{$studioAddress}</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

/**
 * Plain-text fallback, used for the multipart/alternative branch of
 * the email so clients that don't render HTML still get something legible.
 */
function renderReplyEmailPlainText($customerName, $originalMessage, $replyBody, $placement = ''){
    $lines = [];
    $lines[] = "Hi {$customerName},";
    $lines[] = "";
    if ($placement !== ''){
        $lines[] = "Placement concept: {$placement}";
        $lines[] = "";
    }
    $lines[] = $replyBody;
    $lines[] = "";
    $lines[] = "---";
    $lines[] = "Your original message:";
    $lines[] = $originalMessage;
    $lines[] = "";
    $lines[] = STUDIO_NAME;
    $lines[] = STUDIO_ADDRESS;
    return implode("\n", $lines);
}

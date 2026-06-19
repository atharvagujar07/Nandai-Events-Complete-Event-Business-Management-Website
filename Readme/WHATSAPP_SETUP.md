# WhatsApp Meta API PDF Sending Setup

This app sends invoice PDFs through the official Meta WhatsApp Cloud API.

When you click `Send WhatsApp PDF`, the app:

1. Generates the invoice PDF.
2. Uploads the PDF to Meta WhatsApp media API.
3. Sends the PDF as a WhatsApp document message.
4. Saves success/failure in MySQL table `invoice_whatsapp_logs`.

## App-Only Config

The token is used only by this invoice app. Edit only:

```text
config.php
```

Set:

```php
const WHATSAPP_CLOUD_TOKEN = 'your-meta-access-token';
const WHATSAPP_PHONE_NUMBER_ID = 'your-phone-number-id';
const WHATSAPP_GRAPH_VERSION = 'v20.0';
const WHATSAPP_API_BASE = 'https://graph.facebook.com';
```

Do not paste the display phone number. Use the numeric `Phone number ID`.

## Simple Permanent Token Process

1. Go to `https://business.facebook.com/settings`.
2. Open your business account.
3. Go to `Users > System users`.
4. Click `Add`.
5. Name it `Invoice App WhatsApp Sender`.
6. Select the system user.
7. Click `Assign assets`.
8. Assign your Meta app with full access.
9. Click `Generate token`.
10. Choose your app.
11. Select permissions:

```text
whatsapp_business_messaging
whatsapp_business_management
```

12. Generate and copy the token.
13. Paste it into `WHATSAPP_CLOUD_TOKEN` in `config.php`.

If a token was shown in chat or shared anywhere, rotate/regenerate it.

## Get Phone Number ID

1. Go to Meta Developers.
2. Open your app.
3. Go to `WhatsApp > API Setup`.
4. Copy `Phone number ID`.
5. Paste it into `WHATSAPP_PHONE_NUMBER_ID` in `config.php`.

## XAMPP cURL Requirement

Enable PHP cURL:

1. Open `C:\xampp\php\php.ini`.
2. Find:

```ini
;extension=curl
```

3. Change to:

```ini
extension=curl
```

4. Restart Apache.

## Client Phone Format

Client phone must include country code:

```text
+91 9420174884
```

The app removes spaces and symbols before sending.

## Test Mode Notes

If your Meta app is in test mode:

- Add the recipient number as a test recipient in `WhatsApp > API Setup`.
- Verify the recipient number if Meta asks.
- Messages will only deliver to approved test recipients.

For real clients:

- Add a real WhatsApp business phone number.
- Complete Meta business verification if required.
- Use approved templates if messaging outside the 24-hour service window.

## Troubleshooting

Check MySQL table:

```sql
SELECT * FROM invoice_whatsapp_logs ORDER BY sent_at DESC;
```

Common errors:

- `cURL extension is disabled`: enable `extension=curl`.
- `Unsupported post request`: wrong phone number ID or token permission.
- `Recipient phone number not in allowed list`: add test recipient in Meta.
- `Invalid OAuth access token`: token expired or wrong token.
- `Media upload did not return media ID`: token/phone-number ID mismatch.


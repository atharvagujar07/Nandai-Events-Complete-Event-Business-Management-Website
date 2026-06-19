# App-Only SMTP Email Setup

PDF download now works without Composer. The app creates the PDF using plain PHP.

Email sending is configured only inside this invoice app. You do not need to edit XAMPP `php.ini` or `sendmail.ini`.

## Required Database

Import `database.sql`. It creates database:

```sql
invoiceapp
```

It also creates:

- `invoices`
- `invoice_items`
- `invoice_email_logs`

`client_email` is optional. If it is empty, the app can still create and download invoices, but email sending will show an error and save that failed attempt in `invoice_email_logs`.

## Step 1: Create Gmail App Password

1. Open your Google Account.
2. Go to `Security`.
3. Turn on `2-Step Verification`.
4. Search for `App passwords`.
5. Create an app password for Mail.
6. Copy the generated password.

## Step 2: Update This App Only

Open:

```text
config.php
```

Update:

```php
const MAIL_FROM_NAME = 'Nandai Events';
const MAIL_REPLY_TO = 'your-email@gmail.com';
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_SECURE = 'tls';
const SMTP_USERNAME = 'your-email@gmail.com';
const SMTP_PASSWORD = 'your-gmail-app-password';
```

Also enter the sender email in the invoice form. The app uses the saved invoice sender email as Reply-To where possible.

## Step 3: Restart and Test

After changes:

1. Restart Apache in XAMPP.
2. Open an invoice.
3. Click `Send Email`.
4. Check `invoice_email_logs` for sent/failed status.

## If Email Still Fails

- Confirm `client_email` is filled and valid.
- Confirm Apache was restarted.
- Confirm Gmail App Password is used.
- If you see `535-5.7.8 Username and Password not accepted`, your Gmail username/password in `config.php` is wrong for SMTP. Use the full Gmail address and a 16-character Google App Password.
- Check MySQL table `invoice_email_logs`.
- Some networks block SMTP port 587. Try a different network or use a real hosting server.

## Fix Gmail 535 BadCredentials

Use this in `config.php`:

```php
const SMTP_USERNAME = 'your-full-gmail-address@gmail.com';
const SMTP_PASSWORD = 'abcd efgh ijkl mnop';
```

Important:

- `SMTP_USERNAME` must be the full Gmail address.
- `SMTP_PASSWORD` must be a Google App Password.
- Do not use your normal Gmail login password.
- Remove spaces from the App Password if Gmail shows it in groups.
- Gmail account must have 2-Step Verification enabled before App Passwords work.

# Fix: Composer Is Not Recognized on Windows

The `Download PDF` feature uses Dompdf. Dompdf is installed with Composer, so this error means Composer is not installed or not added to Windows PATH.

## Option 1: Install Composer for Windows

1. Download Composer installer from `https://getcomposer.org/download/`.
2. Run `Composer-Setup.exe`.
3. When it asks for PHP path, choose your XAMPP PHP file, usually:

   ```text
   C:\xampp\php\php.exe
   ```

4. Finish installation.
5. Close PowerShell/CMD completely.
6. Open a new PowerShell window.
7. Check Composer:

   ```powershell
   composer --version
   ```

8. Go to the project folder:

   ```powershell
   cd C:\xampp\htdocs\invoice-generator
   ```

9. Install PDF dependency:

   ```powershell
   composer install
   ```

After this, `Download PDF` will work.

## Option 2: Composer Installed but Still Not Working

If Composer is installed but PowerShell still says it is not recognized, restart your computer once. If it still fails, add Composer to PATH manually.

Common Composer PATH:

```text
C:\ProgramData\ComposerSetup\bin
```

Common PHP PATH:

```text
C:\xampp\php
```

Add both to:

```text
Windows Search > Environment Variables > Path > Edit
```

Then open a new PowerShell window and run:

```powershell
composer --version
```

## Option 3: Manual Dompdf Install Without Global Composer

If you do not want Composer globally:

1. Install Composer only temporarily.
2. Run `composer install` inside this project.
3. It will create a `vendor` folder.
4. Copy the full project folder including `vendor` to your server.

The app only needs the generated `vendor` folder at runtime.


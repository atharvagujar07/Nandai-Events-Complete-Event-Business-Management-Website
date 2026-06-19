# PDF Signature Bug: Root Cause and Fixes

## Likely Root Causes

Some uploaded signatures do not appear in generated PDFs because PDF renderers are stricter than browsers.

Common causes:

- Stored `signature_path` is relative but the PDF code needs an absolute filesystem path.
- File was deleted, moved, or upload folder permissions prevent reading.
- Filename contains unsafe characters or spaces and is not resolved correctly.
- Uploaded file is not a valid image even if the extension looks correct.
- PNG/WebP/GIF transparency or color profiles are unsupported by the PDF library.
- GD extension is missing, so PNG/WebP/GIF cannot be converted to PDF-safe JPEG.
- Dompdf/mPDF remote image access is disabled or base path is wrong.
- FPDF/TCPDF cannot render WebP directly.

## Fix Applied in This App

- Upload validation uses MIME detection and `getimagesize()`.
- Upload folders are created automatically.
- File names are sanitized.
- PDF uses absolute paths: `__DIR__ . '/' . signature_path`.
- JPG is embedded directly.
- PNG/WebP/GIF are converted to JPEG bytes using GD `imagecreatefromstring()`.
- Signature is auto-scaled to fit its box while keeping aspect ratio.
- If the file is missing or unsupported, PDF shows `Signature not available`.

## Library Notes

### FPDF

- Works best with JPG and PNG.
- Use absolute path:

```php
$pdf->Image(__DIR__ . '/uploads/signatures/signature.jpg', 150, 250, 40);
```

- Convert WebP/GIF to JPG before calling `Image()`.

### TCPDF

- Supports more image formats than FPDF, but absolute path is still safest.
- Use:

```php
$pdf->Image($absolutePath, 150, 250, 40, 0, '', '', '', false, 300);
```

### mPDF

- Use absolute local paths or valid URLs.
- If using HTML:

```html
<img src="/absolute/path/to/signature.png" style="width:120px">
```

- Check `showImageErrors` during debugging.

### Dompdf

- Enable local/remote loading if needed.
- Use absolute path or base64 data URI.
- PNG transparency can be unreliable depending on GD/Imagick setup.

```php
$options->set('isRemoteEnabled', true);
```

## Testing

Test signatures with:

- JPG photo
- transparent PNG
- WebP
- GIF
- missing/deleted file
- filename with spaces

Expected result: valid images show in PDF; invalid/missing images show fallback text.


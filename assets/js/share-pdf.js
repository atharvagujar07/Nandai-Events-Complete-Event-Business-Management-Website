const shareButton = document.querySelector('#shareWhatsAppPdf');

shareButton?.addEventListener('click', async () => {
  const pdfUrl = shareButton.dataset.pdfUrl;
  const fileName = shareButton.dataset.fileName || 'invoice.pdf';
  const title = shareButton.dataset.shareTitle || 'Invoice PDF';

  try {
    shareButton.disabled = true;
    shareButton.textContent = 'Preparing PDF...';

    const response = await fetch(pdfUrl, { credentials: 'same-origin' });
    if (!response.ok) {
      throw new Error('Could not generate PDF.');
    }

    const blob = await response.blob();
    const file = new File([blob], fileName, { type: 'application/pdf' });

    if (navigator.canShare && navigator.canShare({ files: [file] }) && navigator.share) {
      await navigator.share({
        title,
        text: 'Please find the invoice PDF attached.',
        files: [file],
      });
      return;
    }

    const downloadUrl = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(downloadUrl);
    alert('This browser cannot send PDF files directly to WhatsApp. The PDF has been downloaded. Use Open WhatsApp Chat, then attach this PDF manually.');
  } catch (error) {
    alert(error.message || 'PDF sharing failed.');
  } finally {
    shareButton.disabled = false;
    shareButton.textContent = 'Share PDF';
  }
});

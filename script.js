const statusParams = new URLSearchParams(window.location.search);
if (statusParams.has('status')) {
  setTimeout(() => alert(statusParams.get('status')), 250);
}


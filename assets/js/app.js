const itemsTable = document.querySelector('#itemsTable');
const addItemButton = document.querySelector('#addItem');
const subtotalInput = document.querySelector('#subtotal');
const advanceInput = document.querySelector('#advance');
const totalInput = document.querySelector('#total');
const balanceInput = document.querySelector('#balance');

function money(value) {
  return Number.isFinite(value) ? value.toFixed(2) : '0.00';
}

function renumberItems() {
  const rows = [...itemsTable.querySelectorAll('.item-row:not(.item-title)')];
  rows.forEach((row, index) => {
    row.querySelectorAll('input[name^="items"]').forEach((input) => {
      input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
    });
  });
}

function calculateTotals() {
  let subtotal = 0;
  itemsTable.querySelectorAll('.item-row:not(.item-title)').forEach((row) => {
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const lineTotal = qty * price;
    row.querySelector('.line-total').value = money(lineTotal);
    subtotal += lineTotal;
  });

  const advance = parseFloat(advanceInput.value) || 0;
  subtotalInput.value = money(subtotal);
  totalInput.value = money(subtotal);
  balanceInput.value = money(Math.max(0, subtotal - advance));
}

function createItemRow(index) {
  const row = document.createElement('div');
  row.className = 'item-row';
  row.innerHTML = `
    <input name="items[${index}][description]" placeholder="Item description" required>
    <input class="qty" type="number" name="items[${index}][quantity]" value="1" min="0" step="0.01" required>
    <input class="price" type="number" name="items[${index}][unit_price]" value="0" min="0" step="0.01" required>
    <input class="line-total" value="0.00" readonly>
    <button class="icon-button remove-item" type="button" title="Delete item">×</button>
  `;
  return row;
}

addItemButton?.addEventListener('click', () => {
  const count = itemsTable.querySelectorAll('.item-row:not(.item-title)').length;
  itemsTable.appendChild(createItemRow(count));
  calculateTotals();
});

itemsTable?.addEventListener('input', (event) => {
  if (event.target.matches('.qty, .price')) {
    calculateTotals();
  }
});

itemsTable?.addEventListener('click', (event) => {
  if (!event.target.matches('.remove-item')) {
    return;
  }

  const rows = itemsTable.querySelectorAll('.item-row:not(.item-title)');
  if (rows.length === 1) {
    rows[0].querySelector('input[name$="[description]"]').value = '';
    rows[0].querySelector('.qty').value = '1';
    rows[0].querySelector('.price').value = '0';
  } else {
    event.target.closest('.item-row').remove();
  }

  renumberItems();
  calculateTotals();
});

advanceInput?.addEventListener('input', calculateTotals);
calculateTotals();


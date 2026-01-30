// Auto-fill address from saved addresses dropdown
function fillAddressFromSaved() {
    const select = document.getElementById('saved_address_select');
    if (!select) return;
    
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value === 'manual' || selectedOption.value === '') {
        // Enable manual input
        document.getElementById('name').readOnly = false;
        document.getElementById('phone').readOnly = false;
        document.getElementById('address').readOnly = false;
        
        if (selectedOption.value === 'manual') {
            // Clear fields for manual entry (keep email)
            document.getElementById('name').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('address').value = '';
        }
    } else {
        // Fill from selected address
        const name = selectedOption.getAttribute('data-name');
        const phone = selectedOption.getAttribute('data-phone');
        const address = selectedOption.getAttribute('data-address');
        
        document.getElementById('name').value = name || '';
        document.getElementById('phone').value = phone || '';
        document.getElementById('address').value = address || '';
        
        // Make fields read-only when using saved address (better UX)
        document.getElementById('name').readOnly = true;
        document.getElementById('phone').readOnly = true;
        document.getElementById('address').readOnly = true;
    }
}

// Auto-fill default address on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedAddressSelect = document.getElementById('saved_address_select');
    if (savedAddressSelect && savedAddressSelect.value !== '') {
        fillAddressFromSaved();
    }
});

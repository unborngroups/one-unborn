// GSTIN by PAN Fetch Script for Client Master with Multiple Selection
// This script fetches GSTIN details when PAN is entered and allows selecting multiple GSTINs

let fetchedGstins = [];
let selectedGstins = [];

function fetchGSTINsByPAN() {
    let pan = document.getElementById("pan_number").value.trim().toUpperCase();
    let gstStatus = document.getElementById("gstStatus");

    if (pan.length !== 10) {
        gstStatus.innerHTML = "⚠️ Enter valid 10-digit PAN";
        return;
    }

    gstStatus.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Fetching GSTIN details...';

    fetch(window.location.origin + '/clients/fetch-gstin-by-pan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ pan_number: pan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            fetchedGstins = data.data;
            displayGSTINOptions(data.data);
            gstStatus.innerHTML = `✅ Found ${data.data.length} GSTIN(s) for this PAN - Select one or more`;
        } else {
            gstStatus.innerHTML = data.message || "❌ No GSTIN found for this PAN";
            fetchedGstins = [];
        }
    })
    .catch(error => {
        console.error('Error:', error);
        gstStatus.innerHTML = "⚠️ Unable to fetch GSTIN details. Please enter manually.";
    });
}

function displayGSTINOptions(gstins) {
    // Remove any existing GSTIN selection div
    const existingDiv = document.querySelector('.gstin-selection-div');
    if (existingDiv) existingDiv.remove();
    
    let html = '<div class="card mt-3 p-3 bg-light gstin-selection-div">';
    html += '<h6 class="text-primary mb-3">Select GSTIN(s) - You can select multiple locations:</h6>';
    
    gstins.forEach((gstin, index) => {
        const tradeLine = gstin.trade_name ? `<strong>Trade Name:</strong> ${gstin.trade_name}<br>` : '';
        const legalLine = gstin.legal_name ? `<strong>Legal Name:</strong> ${gstin.legal_name}<br>` : '';
        const addressLine = `<strong>Address:</strong> ${gstin.principal_business_address || ''}<br>`;
        const stateLine = `<strong>State:</strong> ${gstin.state || ''} | <strong>Pincode:</strong> ${gstin.pincode || ''}<br>`;
        html += `
            <div class="form-check mb-3 p-3 border rounded" style="background: white;">
                <div class="row">
                    <div class="col-md-10">
                        <input class="form-check-input" type="checkbox" 
                               id="gstin_${index}" value="${index}" 
                               onchange="handleGSTINSelection(${index})">
                        <label class="form-check-label" for="gstin_${index}">
                            <strong class="text-primary">${gstin.gstin}</strong><br>
                            ${tradeLine}
                            ${legalLine}
                            ${addressLine}
                            ${stateLine}
                        </label>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="setAsPrimary(${index})" 
                                id="primary_btn_${index}" style="display:none;">
                            Set as Primary
                        </button>
                        <span class="badge bg-success" id="primary_badge_${index}" style="display:none;">
                            Primary
                        </span>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="mt-3">
            <button type="button" class="btn btn-success" onclick="saveSelectedGSTINs()" id="saveGstinsBtn" style="display:none;">
                <i class="bi bi-check-circle"></i> Save Selected GSTIN(s)
            </button>
            <button type="button" class="btn btn-primary" onclick="fillPrimaryGSTINDetails()" id="fillDetailsBtn" style="display:none;">
                <i class="bi bi-pencil"></i> Auto-fill with Primary GSTIN
            </button>
            <span class="text-muted ms-2" id="selectionCount"></span>
        </div>
    `;
    html += '</div>';
    
    document.getElementById("gstStatus").insertAdjacentHTML('afterend', html);
}

function handleGSTINSelection(index) {
    const checkbox = document.getElementById(`gstin_${index}`);
    const primaryBtn = document.getElementById(`primary_btn_${index}`);
    
    if (checkbox.checked) {
        // Add to selected list
        if (!selectedGstins.find(g => g.gstin === fetchedGstins[index].gstin)) {
            selectedGstins.push({...fetchedGstins[index], index: index});
        }
        primaryBtn.style.display = 'inline-block';
        
        // If this is the first selection, make it primary automatically
        if (selectedGstins.length === 1) {
            setAsPrimary(index);
        }
    } else {
        // Remove from selected list
        selectedGstins = selectedGstins.filter(g => g.index !== index);
        primaryBtn.style.display = 'none';
        
        // Hide primary badge
        const primaryBadge = document.getElementById(`primary_badge_${index}`);
        if (primaryBadge) primaryBadge.style.display = 'none';
        
        // If removed item was primary, make first selected item primary
        if (fetchedGstins[index].is_primary && selectedGstins.length > 0) {
            setAsPrimary(selectedGstins[0].index);
        }
    }
    
    updateSelectionUI();
}

function setAsPrimary(index) {
    // Clear all primary flags
    selectedGstins.forEach(g => {
        g.is_primary = false;
        const badge = document.getElementById(`primary_badge_${g.index}`);
        const btn = document.getElementById(`primary_btn_${g.index}`);
        if (badge) badge.style.display = 'none';
        if (btn) btn.style.display = 'inline-block';
    });
    
    // Set this one as primary
    const gstin = selectedGstins.find(g => g.index === index);
    if (gstin) {
        gstin.is_primary = true;
        fetchedGstins[index].is_primary = true;
        
        const badge = document.getElementById(`primary_badge_${index}`);
        const btn = document.getElementById(`primary_btn_${index}`);
        if (badge) badge.style.display = 'inline-block';
        if (btn) btn.style.display = 'none';
    }
}

function updateSelectionUI() {
    const saveBtn = document.getElementById('saveGstinsBtn');
    const fillBtn = document.getElementById('fillDetailsBtn');
    const countSpan = document.getElementById('selectionCount');
    
    if (selectedGstins.length > 0) {
        saveBtn.style.display = 'inline-block';
        fillBtn.style.display = 'inline-block';
        countSpan.textContent = `${selectedGstins.length} GSTIN(s) selected`;
    } else {
        saveBtn.style.display = 'none';
        fillBtn.style.display = 'none';
        countSpan.textContent = '';
    }
}

function fillPrimaryGSTINDetails() {
    const primaryGstin = selectedGstins.find(g => g.is_primary) || selectedGstins[0];
    
    if (!primaryGstin) {
        alert('Please select at least one GSTIN');
        return;
    }
    
    // Fill GSTIN field
    if (document.getElementById("gstin")) {
        document.getElementById("gstin").value = primaryGstin.gstin || '';
    }
    
    // Fill Business Name
    if (primaryGstin.trade_name && document.getElementById("business_display_name")) {
        document.getElementById("business_display_name").value = primaryGstin.trade_name;
    }
    
    // Fill Address
    if (primaryGstin.principal_business_address) {
        const addressParts = primaryGstin.principal_business_address.split(', ');
        if (document.getElementById("address1")) {
            document.getElementById("address1").value = addressParts.slice(0, 3).join(', ') || '';
        }
        
        // Fill state
        if (primaryGstin.state) {
            const stateSelect = document.querySelector('select[name="state"]');
            if (stateSelect) {
                let option = Array.from(stateSelect.options).find(opt => 
                    opt.value.toLowerCase() === primaryGstin.state.toLowerCase()
                );
                if (!option) {
                    option = new Option(primaryGstin.state, primaryGstin.state, true, true);
                    stateSelect.add(option);
                }
                stateSelect.value = primaryGstin.state;
            }
        }
    }
    
    // Fill Pincode
    if (primaryGstin.pincode) {
        const pincodeInput = document.querySelector('input[name="pincode"]');
        if (pincodeInput) {
            pincodeInput.value = primaryGstin.pincode;
        }
    }
    
    alert('Form fields filled with primary GSTIN details');
}

function saveSelectedGSTINs() {
    if (selectedGstins.length === 0) {
        alert('Please select at least one GSTIN');
        return;
    }
    
    // Get client_id from form or URL
    const urlParams = new URLSearchParams(window.location.search);
    const clientId = document.querySelector('input[name="client_id"]')?.value 
                     || urlParams.get('id') 
                     || window.location.pathname.split('/').filter(Boolean).pop();
    
    if (!clientId || isNaN(clientId)) {
        alert('Please save the client first, then select GSTINs');
        return;
    }
    
    const saveBtn = document.getElementById('saveGstinsBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
    
    fetch(window.location.origin + '/clients/save-selected-gstins', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            client_id: parseInt(clientId),
            gstins: selectedGstins
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Optionally reload or update UI
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving GSTINs');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> Save Selected GSTIN(s)';
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const panInput = document.getElementById("pan_number");
    if (panInput) {
        panInput.addEventListener("blur", fetchGSTINsByPAN);
    }
});

// ── Module-level state, OUTSIDE DOMContentLoaded ──────────────────
let ALL_SECTOR_DATA = null;   // { subsectorsBySector: { sectorId: [{id, name}, ...] } }
let cascadeGroups = [];
let subSelectIds = [];

// ── Load all sector/subsector data ONCE per page load ──────────────
async function loadAllSectorData() {
    if (ALL_SECTOR_DATA) return ALL_SECTOR_DATA;
    try {
        const res = await fetch(`${BASE_URL}/all-sectors-subsectors`);
        ALL_SECTOR_DATA = await res.json();
    } catch (err) {
        console.error('Failed to load sector/subsector data:', err);
        ALL_SECTOR_DATA = { subsectorsBySector: {} };
    }
    return ALL_SECTOR_DATA;
}

function getSelectedSubValues(excludeId) {
    return subSelectIds
        .filter(id => id !== excludeId)
        .map(id => document.getElementById(id).value)
        .filter(val => val !== '');
}

function renderSubOptions(subSelect) {
    const subId = subSelect.id;

    const sectorSelect = cascadeGroups.find(
        sel => sel.getAttribute('data-cascade-target') === subId
    );
    const sectorId = sectorSelect?.value;

    const currentValue = subSelect.value; // capture BEFORE wiping

    subSelect.innerHTML = '<option value="">-- ንዑስ ዘርፍ ይምረጡ --</option>';

    if (!sectorId || !ALL_SECTOR_DATA) {
        subSelect.disabled = true;
        return;
    }

    const options = ALL_SECTOR_DATA.subsectorsBySector[sectorId] || [];
    const selectedElsewhere = getSelectedSubValues(subId);

    if (!options.length) {
        subSelect.innerHTML = '<option value="">-- ንዑስ ዘርፍ የለም --</option>';
        subSelect.disabled = true;
        return;
    }

    options.forEach(function (sub) {
        const isTakenElsewhere = selectedElsewhere.includes(String(sub.id));
        const opt = document.createElement('option');
        opt.value = sub.id;
        opt.textContent = isTakenElsewhere ? `${sub.name} (ተመርጧል)` : sub.name;
        // no opt.disabled — stays selectable either way
        subSelect.appendChild(opt);
    });

    subSelect.disabled = false;

    if (currentValue && options.some(o => String(o.id) === currentValue)) {
        subSelect.value = currentValue;
    }
}
function clearConflictingDownstreamSelections(changedId, newValue) {
    const changedIndex = subSelectIds.indexOf(changedId);
    if (changedIndex === -1 || !newValue) return;

    subSelectIds.forEach(function (id, index) {
        if (index <= changedIndex) return; // only later selects

        const subSelect = document.getElementById(id);
        if (subSelect && subSelect.value === newValue) {
            subSelect.selectedIndex = 0; // reset to the default placeholder option
        }
    });
}

// ── FIX: accepts an excludeId so the select the user just interacted
// with isn't rebuilt out from under their own click. Only the OTHER
// sub-selects get refreshed, to grey out options now taken elsewhere. ──
function refreshSubsequentSubSelects(changedId) {
    const changedIndex = subSelectIds.indexOf(changedId);
    if (changedIndex === -1) return;

    subSelectIds.forEach(function (id, index) {
        if (index <= changedIndex) return;
        const subSelect = document.getElementById(id);
        renderSubOptions(subSelect);
    });
}

window.loadSubsectorsFor = function (sectorSelect) {
    const targetId = sectorSelect.getAttribute('data-cascade-target');
    const subSelect = document.getElementById(targetId);
    if (subSelect) renderSubOptions(subSelect);
};

document.addEventListener('DOMContentLoaded', async function () {
    cascadeGroups = Array.from(document.querySelectorAll('[data-cascade-target]'));
    subSelectIds = cascadeGroups.map(sel => sel.getAttribute('data-cascade-target'));

    await loadAllSectorData();

    cascadeGroups.forEach(function (sectorSelect) {
        sectorSelect.addEventListener('change', function () {
            window.loadSubsectorsFor(sectorSelect);
        });
    });

    subSelectIds.forEach(function (id) {
    document.getElementById(id)?.addEventListener('change', function () {
        const changedSelect = document.getElementById(id);
        const newValue = changedSelect.value;

        clearConflictingDownstreamSelections(id, newValue);
        refreshSubsequentSubSelects(id);
    });
});
});
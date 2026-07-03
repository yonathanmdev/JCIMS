// ── Module-level state, OUTSIDE DOMContentLoaded ──────────────────
const subOptionsCache = {};
let cascadeGroups = [];
let subSelectIds = [];

function getSelectedSubValues(excludeId) {
    return subSelectIds
        .filter(id => id !== excludeId)
        .map(id => document.getElementById(id).value)
        .filter(val => val !== '');
}

function renderSubOptions(subSelect) {
    const subId = subSelect.id;
    const options = subOptionsCache[subId] || [];
    const selectedElsewhere = getSelectedSubValues(subId);
    const currentValue = subSelect.value;

    subSelect.innerHTML = '<option value="">-- ንዑስ ዘርፍ ይምረጡ --</option>';

    options.forEach(function (sub) {
        const isTakenElsewhere = selectedElsewhere.includes(String(sub.id));
        const opt = document.createElement('option');
        opt.value = sub.id;
        opt.textContent = sub.subsector;
        if (isTakenElsewhere) {
            opt.disabled = true;
            opt.textContent += ' (ተመርጧል)';
        }
        subSelect.appendChild(opt);
    });

    if (currentValue && options.some(o => String(o.id) === currentValue)) {
        subSelect.value = currentValue;
    }
}

function refreshAllSubSelects() {
    subSelectIds.forEach(function (id) {
        const subSelect = document.getElementById(id);
        if (!subSelect.disabled) {
            renderSubOptions(subSelect);
        }
    });
}

// ── Shared loader — callable from a real "change" event AND programmatically ──
window.loadSubsectorsFor = async function (sectorSelect) {
    const targetId = sectorSelect.getAttribute('data-cascade-target');
    const subSelect = document.getElementById(targetId);
    const sectorId = sectorSelect.value;

    subOptionsCache[targetId] = [];

    // ⚡ minimal DOM work (do NOT force reflow early)
    subSelect.disabled = true;
    subSelect.innerHTML = '<option>Loading...</option>';

    if (!sectorId) {
        subSelect.innerHTML = '<option>-- መጀመሪያ ዘርፍ ይምረጡ --</option>';
        return;
    }

    try {
        const res = await fetch(
            `${BASE_URL}/subsectors-by-sector?sector_id=${encodeURIComponent(sectorId)}`
        );

        const subsectors = await res.json();
        subOptionsCache[targetId] = subsectors;

        if (!subsectors.length) {
            subSelect.innerHTML = '<option>-- ንዑስ ዘርፍ የለም --</option>';
            return;
        }

        // FIX: route freshly-loaded options through renderSubOptions instead of
        // building the option list by hand. Building it manually skipped the
        // "already selected in another sector" disabling logic entirely, so a
        // subsector could be picked twice across the cascade groups until some
        // other select happened to change and trigger a refresh.
        requestAnimationFrame(() => {
            renderSubOptions(subSelect);
            subSelect.disabled = false;
        });

    } catch (err) {
        console.error('Subsector fetch failed:', err);
        subSelect.innerHTML = '<option>-- ስህተት ተከስቷል --</option>';
    }
};

// ── DOMContentLoaded: only wiring, no logic definitions ────────────
document.addEventListener('DOMContentLoaded', function () {
    cascadeGroups = Array.from(document.querySelectorAll('[data-cascade-target]'));
    subSelectIds = cascadeGroups.map(sel => sel.getAttribute('data-cascade-target'));

    cascadeGroups.forEach(function (sectorSelect) {
        sectorSelect.addEventListener('change', function () {
            window.loadSubsectorsFor(sectorSelect);   // path 1: real user selection
        });
    });

    subSelectIds.forEach(function (id) {
        document.getElementById(id).addEventListener('change', refreshAllSubSelects);
    });
});
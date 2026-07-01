document.addEventListener('DOMContentLoaded', function () {
    const cascadeGroups = Array.from(document.querySelectorAll('[data-cascade-target]'));
    const subSelectIds = cascadeGroups.map(sel => sel.getAttribute('data-cascade-target'));

    // Cache each subsector select's currently loaded options (id -> label)
    const subOptionsCache = {};

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
                opt.textContent += ' (ተመርጧል)'; // "(already selected)"
            }
            subSelect.appendChild(opt);
        });

        // Restore previous selection if it's still valid/available
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

    cascadeGroups.forEach(function (sectorSelect) {
        sectorSelect.addEventListener('change', async function () {
            const targetId = this.getAttribute('data-cascade-target');
            const subSelect = document.getElementById(targetId);
            const sectorId = this.value;

            subOptionsCache[targetId] = [];
            subSelect.innerHTML = '<option value="">-- በመጫን ላይ... --</option>';
            subSelect.disabled = true;

            if (!sectorId) {
                subSelect.innerHTML = '<option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>';
                return;
            }

            try {
               const res = await fetch(`${BASE_URL}/subsectors-by-sector?sector_id=${encodeURIComponent(sectorId)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Network response was not ok');
                const subsectors = await res.json();

                subOptionsCache[targetId] = subsectors;

                if (!subsectors.length) {
                    subSelect.innerHTML = '<option value="">-- ንዑስ ዘርፍ አልተገኘም --</option>';
                    subSelect.disabled = true;
                    return;
                }

                subSelect.disabled = false;
                renderSubOptions(subSelect);
            } catch (err) {
                console.error('Subsector fetch failed:', err);
                subSelect.innerHTML = '<option value="">-- ስህተት ተከስቷል --</option>';
            }
        });
    });

    // When any subsector selection changes, refresh the others to gray out the newly taken value
    subSelectIds.forEach(function (id) {
        document.getElementById(id).addEventListener('change', refreshAllSubSelects);
    });
});
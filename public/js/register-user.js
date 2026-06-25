document.addEventListener("DOMContentLoaded", function () {

    const roleSelector      = document.getElementById("roleSelector");
    const orgSelector       = document.getElementById("orgSelector");
    const orgSelectorHidden = document.getElementById("orgSelectorHidden");
    const sessionOrgId      = orgSelector.dataset.sessionOrg;
    const sessionBranchName = orgSelector.dataset.sessionBranchName;

    function syncHidden() {
        orgSelectorHidden.value = orgSelector.value;
        console.log("synced hidden to:", orgSelectorHidden.value); // debug
    }

    function handleRoleChange() {
        const role = roleSelector.value;

        if (SESSION_ROLE === 'system_admin') {
            orgSelector.disabled = false;
            syncHidden();
            return;
        }

        if (role === 'org_admin') {
            orgSelector.disabled = false;

            let options = `<option value="">-- ቅርንጫፍ ይምረጡ --</option>`;
            SUB_BRANCHES.forEach(function (branch) {
                options += `<option value="${branch.id}">${branch.name}</option>`;
            });
            orgSelector.innerHTML = options;
            orgSelectorHidden.value = ""; // reset until user picks

        } else if (role === 'team_leader' || role === 'officer') {
            orgSelector.innerHTML =
                `<option value="${sessionOrgId}" selected>${sessionBranchName}</option>`;
            orgSelector.disabled = true;
            orgSelectorHidden.value = sessionOrgId;
            console.log("hr branch set to:", orgSelectorHidden.value); // debug

        } else {
            orgSelector.innerHTML = `<option value="">-- ተቁሙን ይምረጡ --</option>`;
            orgSelector.disabled  = true;
            orgSelectorHidden.value = "";
        }
    }

    // sync whenever user picks from orgSelector
    orgSelector.addEventListener("change", function () {
        syncHidden();
        console.log("user picked:", orgSelector.value); // debug
    });

    handleRoleChange();
    roleSelector.addEventListener("change", handleRoleChange);
});
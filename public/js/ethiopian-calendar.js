(function() {
    /* -----------------------------------------------------------
        1. የቀን መቀየሪያ ሎጂኮች (ሙሉ በሙሉ ያንተ ኮድ)
    ----------------------------------------------------------- */
    
    // ኢትዮጵያ ወደ ግሪጎርያን
    function ethiopianToGregorian(ethDay, ethMonth, ethYear) {
        let leapEffect = 0, gDay = 0, gMonth = 0, gYear = 0, gLeap = 0;
        if ((((ethYear - 1) + 5500) % 4) == 3) leapEffect = 1;

        if (ethMonth == 1) {
            gYear = ethYear + 7;
            if (ethDay <= (20 - leapEffect)) { gMonth = 9; gDay = ethDay + 10 + leapEffect; }
            else { gMonth = 10; gDay = (leapEffect == 1) ? ethDay - 19 : ethDay - 20; }
        } else if (ethMonth == 2) {
            gYear = ethYear + 7;
            if (ethDay <= (21 - leapEffect)) { gMonth = 10; gDay = ethDay + 10 + leapEffect; }
            else { gMonth = 11; gDay = (leapEffect == 1) ? ethDay - 20 : ethDay - 21; }
        } else if (ethMonth == 3) {
            gYear = ethYear + 7;
            if (ethDay <= (21 - leapEffect)) { gMonth = 11; gDay = ethDay + 9 + leapEffect; }
            else { gMonth = 12; gDay = (leapEffect == 1) ? ethDay - 20 : ethDay - 21; }
        } else if (ethMonth == 4) {
            if (ethDay <= (22 - leapEffect)) { gYear = ethYear + 7; gMonth = 12; gDay = ethDay + 9 + leapEffect; }
            else { gYear = ethYear + 8; gMonth = 1; gDay = (leapEffect == 1) ? ethDay - 21 : ethDay - 22; }
        } else if (ethMonth == 5) {
            gYear = ethYear + 8;
            if (ethDay <= (23 - leapEffect)) { gMonth = 1; gDay = ethDay + 8 + leapEffect; }
            else { gMonth = 2; gDay = (leapEffect == 1) ? ethDay - 22 : ethDay - 23; }
        } else if (ethMonth == 6) {
            gYear = ethYear + 8;
            if (gYear % 4 === 0 && (gYear % 100 !== 0 || gYear % 400 === 0)) gLeap = 1;
            if (ethDay <= (21 + gLeap - leapEffect)) { gMonth = 2; gDay = ethDay + 7 + leapEffect; }
            else { gMonth = 3; gDay = (leapEffect == 1) ? ethDay - (20 + gLeap) : ethDay - (21 + gLeap); }
        } else if (ethMonth >= 7 && ethMonth <= 13) {
            gYear = ethYear + 8;
            if (ethMonth == 7) { gMonth = (ethDay <= 22) ? 3 : 4; gDay = (ethDay <= 22) ? ethDay + 9 : ethDay - 22; }
            else if (ethMonth == 8) { gMonth = (ethDay <= 22) ? 4 : 5; gDay = (ethDay <= 22) ? ethDay + 8 : ethDay - 22; }
            else if (ethMonth == 9) { gMonth = (ethDay <= 23) ? 5 : 6; gDay = (ethDay <= 23) ? ethDay + 8 : ethDay - 23; }
            else if (ethMonth == 10) { gMonth = (ethDay <= 23) ? 6 : 7; gDay = (ethDay <= 23) ? ethDay + 7 : ethDay - 23; }
            else if (ethMonth == 11) { gMonth = (ethDay <= 24) ? 7 : 8; gDay = (ethDay <= 24) ? ethDay + 7 : ethDay - 24; }
            else if (ethMonth == 12) { gMonth = (ethDay <= 25) ? 8 : 9; gDay = (ethDay <= 25) ? ethDay + 6 : ethDay - 25; }
            else if (ethMonth == 13) { gMonth = 9; gDay = ethDay + 5; }
        }
        return { year: gYear,  month: gMonth, day: gDay };
    }

    // ግሪጎርያን ወደ ኢትዮጵያ
    function gerigoriantoEthiopian(day, month, year) {
        let ethyear = 0, ethmonth = 0, ethday = 0, ethleapEffect = 0, ethleapEffect2 = 0;
        if ((((year - 9) + 5500) % 4) == 3) ethleapEffect = 1;

        if (month == 1) {
            ethyear = year - 8;
            if (day <= (8 + ethleapEffect)) { ethmonth = 4; ethday = (day + 22 - ethleapEffect); }
            else { ethmonth = 5; ethday = (ethleapEffect == 1) ? day - 9 : day - 8; }
        } else if (month == 2) {
            ethyear = year - 8;
            if (day <= (7 + ethleapEffect)) { ethmonth = 5; ethday = (day + 23 - ethleapEffect); }
            else { ethmonth = 6; ethday = (ethleapEffect == 1) ? day - 8 : day - 7; }
        } else if (month == 3) {
            ethyear = year - 8;
            if (day <= 9) { ethmonth = 6; ethday = (day + 21); }
            else { ethmonth = 7; ethday = day - 9; }
        } else if (month == 4) {
            ethyear = year - 8;
            if (day <= 8) { ethmonth = 7; ethday = (day + 22); }
            else { ethmonth = 8; ethday = day - 8; }
        } else if (month >= 5 && month <= 12) {
            if (month == 9) {
                if ((((year - 8) + 5500) % 4) == 3) ethleapEffect2 = 1;
                if (day <= 5) { ethyear = year - 8; ethmonth = 12; ethday = day + 25; }
                else if (day >= 6 && day <= (10 + ethleapEffect2)) { ethyear = year - 8; ethmonth = 13; ethday = day - 5; }
                else { ethyear = year - 7; ethmonth = 1; ethday = (ethleapEffect2 == 1) ? day - 11 : day - 10; }
            } else {
                ethyear = (month >= 10 || (month == 9 && day > 10)) ? year - 7 : year - 8;
                if (month == 5) { ethmonth = (day <= 8) ? 8 : 9; ethday = (day <= 8) ? day + 22 : day - 8; }
                if (month == 6) { ethmonth = (day <= 7) ? 9 : 10; ethday = (day <= 7) ? day + 23 : day - 7; }
                if (month == 7) { ethmonth = (day <= 7) ? 10 : 11; ethday = (day <= 7) ? day + 23 : day - 7; }
                if (month == 8) { ethmonth = (day <= 6) ? 11 : 12; ethday = (day <= 6) ? day + 24 : day - 6; }
                if (month == 10) { ethmonth = (day <= 10) ? 1 : 2; ethday = (day <= 10) ? day + 20 : day - 10; ethyear = year - 7; }
                if (month == 11) { ethmonth = (day <= 9) ? 2 : 3; ethday = (day <= 9) ? day + 21 : day - 9; ethyear = year - 7; }
                if (month == 12) { ethmonth = (day <= 9) ? 3 : 4; ethday = (day <= 9) ? day + 21 : day - 9; ethyear = year - 7; }
            }
        }
        return { day: ethday, month: ethmonth, year: ethyear };
    }

    const now = new Date();
    const today = gerigoriantoEthiopian(now.getDate(), now.getMonth() + 1, now.getFullYear());
    const ET_MONTHS = ["መስከረም", "ጥቅምት", "ኅዳር", "ታኅሣስ", "ጥር", "የካቲት", "መጋቢት", "ሚያዝያ", "ግንቦት", "ሰኔ", "ሐምሌ", "ነሐሴ", "ጳጉሜ"];

    /* -----------------------------------------------------------
        2. UI/CSS - ለሞዳል ተኳሃኝ የሆነ (Fixed & High Z-index)
    ----------------------------------------------------------- */
    const style = document.createElement('style');
style.textContent = `
    .eth-cal-popup { 
        position: fixed;
        width: 320px; 
        background: #fff; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.4); 
        border-radius: 8px; 
        padding: 12px; 
        z-index: 999999 !important;
        display: none; 
        border: 1px solid #ccc;
        font-family: 'Segoe UI', sans-serif;

        /* ← Remove any fixed height — let it grow naturally */
        height: auto;
        min-height: unset;
        max-height: unset;
        overflow: visible;
    }
    .cal-head { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        background: #1A1208; 
        color: #C8962A; 
        padding: 8px; 
        border-radius: 5px; 
        position: relative; /* needed for dropdown lists */
    }
    .grid-days { 
        display: grid; 
        grid-template-columns: repeat(7, 1fr); 
        gap: 2px; 
        text-align: center; 
        margin-top: 10px; 
    }
    .grid-days div { 
        padding: 6px 0;   /* ← slightly reduced from 8px to keep compact */
        cursor: pointer; 
        border-radius: 4px; 
        font-size: 13px; 
        min-height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .grid-days div:hover { background: #eee; }
    .grid-days .today { 
        outline: 2px solid #C8962A; 
        font-weight: bold; 
        background: #fff9eb; 
    }
    .grid-days .selected-day {
        background: #C8962A;
        color: #fff;
        font-weight: bold;
        border-radius: 4px;
    }
    .grid-days .empty {
        cursor: default;  /* ← empty cells not clickable */
        pointer-events: none;
    }
    .drop-list { 
        position: absolute; 
        background: #fff; 
        border: 1px solid #ddd; 
        max-height: 180px; 
        overflow-y: auto; 
        z-index: 1000000; 
        width: 100px; 
        display: none; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2); 
    }
`;
    document.head.appendChild(style);

    const popup = document.createElement('div');
    popup.className = 'eth-cal-popup';
    popup.innerHTML = `
        <div class="cal-head">
            <button id="prevM" style="color:#C8962A; background:none; border:none; cursor:pointer; font-size:20px;">&lsaquo;</button>
            <div><span id="mDisp" style="cursor:pointer"></span> <span id="yDisp" style="cursor:pointer"></span></div>
            <button id="nextM" style="color:#C8962A; background:none; border:none; cursor:pointer; font-size:20px;">&rsaquo;</button>
            <div id="mList" class="drop-list"></div>
            <div id="yList" class="drop-list" style="right: 10px;"></div>
        </div>
        <div class="grid-days" style="color:#666; font-size:11px; pointer-events:none;">
            <div>እሁ</div><div>ሰኞ</div><div>ማክ</div><div>ረቡ</div><div>ሐሙ</div><div>ዓር</div><div>ቅዳ</div>
        </div>
        <div class="grid-days" id="daysContainer"></div>
    `;
    document.body.appendChild(popup);

    let activeInput = null, viewY = today.year, viewM = today.month;

    /* -----------------------------------------------------------
        3. Position & Render Logic
    ----------------------------------------------------------- */
    function updatePosition() {
    if (!activeInput || popup.style.display !== 'block') return;

    const rect = activeInput.getBoundingClientRect();
    const popupHeight = popup.offsetHeight || 350; // የካላንደሩ ቁመት (ግምት)
    const windowHeight = window.innerHeight;

    // ወደ ታች በቂ ቦታ አለ ወይ? (ከታች ቢያንስ የካላንደሩን ቁመት ያህል ቦታ ይፈልጋል)
    const spaceBelow = windowHeight - rect.bottom;
    const hasSpaceBelow = spaceBelow > popupHeight;

    if (hasSpaceBelow) {
        // በቂ ቦታ ካለ ወደ ታች ይከፈታል
        popup.style.top = (rect.bottom + 5) + 'px';
    } else {
        // በቂ ቦታ ከሌለ ወደ ላይ ይከፈታል
        popup.style.top = (rect.top - popupHeight - 5) + 'px';
    }

    popup.style.left = rect.left + 'px';
}
    // Modal ውስጥ ስክሮል ሲደረግ ካላንደሩ እንዲከተል
    window.addEventListener('scroll', updatePosition, true);
    window.addEventListener('resize', updatePosition);

    function render() {
        popup.querySelector('#mDisp').textContent = ET_MONTHS[viewM - 1];
        popup.querySelector('#yDisp').textContent = viewY;
        const container = popup.querySelector('#daysContainer');
        container.innerHTML = '';

        const gStart = ethiopianToGregorian(1, viewM, viewY);
        const startIdx = new Date(gStart.year, gStart.month - 1, gStart.day).getDay();
        const monthLen = (viewM === 13) ? ((viewY % 4 === 3) ? 6 : 5) : 30;

        for (let i = 0; i < startIdx; i++) container.appendChild(document.createElement('div'));
        for (let d = 1; d <= monthLen; d++) {
            const cell = document.createElement('div');
            cell.textContent = d;
            if (d === today.day && viewM === today.month && viewY === today.year) cell.className = 'today';
            cell.onclick = (e) => {
                e.stopPropagation();
                const pad = (n) => String(n).padStart(2, '0');
                activeInput.value = `${pad(d)}/${pad(viewM)}/${viewY}`;
                
                const g = ethiopianToGregorian(d, viewM, viewY);
                const target = document.querySelector(activeInput.dataset.gregorian);
                if (target) target.value = `${g.year}-${pad(g.month)}-${pad(g.day)}`;
                
                popup.style.display = 'none';
            };
            container.appendChild(cell);
        }
    }

    document.addEventListener('focusin', (e) => {
        if (e.target.classList.contains('ethiopian-date')) {
            activeInput = e.target;
            popup.style.display = 'block';
            updatePosition();
            render();
        }
    });

    document.addEventListener('mousedown', (e) => {
        if (!popup.contains(e.target) && e.target !== activeInput) {
            popup.style.display = 'none';
        }
    });

    // Dropdown ለወራት እና ለዓመታት
    popup.querySelector('#mDisp').onclick = (e) => {
        e.stopPropagation();
        const list = popup.querySelector('#mList');
        list.innerHTML = ''; list.style.display = 'block';
        ET_MONTHS.forEach((m, i) => {
            const d = document.createElement('div'); d.textContent = m;
            d.onclick = (ev) => { ev.stopPropagation(); viewM = i + 1; list.style.display = 'none'; render(); };
            list.appendChild(d);
        });
    };

    popup.querySelector('#yDisp').onclick = (e) => {
        e.stopPropagation();
        const list = popup.querySelector('#yList');
        list.innerHTML = ''; list.style.display = 'block';
        for (let i = 1900; i <= today.year + 5; i++) {
            const d = document.createElement('div'); d.textContent = i;
            d.onclick = (ev) => { ev.stopPropagation(); viewY = i; list.style.display = 'none'; render(); };
            list.appendChild(d);
        }
        list.scrollTop = list.scrollHeight;
    };

    popup.querySelector('#prevM').onclick = () => { viewM--; if (viewM < 1) { viewM = 13; viewY--; } render(); };
    popup.querySelector('#nextM').onclick = () => { viewM++; if (viewM > 13) { viewM = 1; viewY++; } render(); };
})();
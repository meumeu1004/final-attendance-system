// Shared by adminhome.html, attendance-records.html, classlists.html.
// Each feature only runs if the elements exist on the current page.
(() => {
    const $ = (sel, root = document) => root.querySelector(sel);

    /* ---------- Logout ---------- */
    document.querySelectorAll('form[action$="/logout"]').forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (!confirm("Log out?")) event.preventDefault();
        });
    });

    /* ---------- Table search + filters (records, class lists) ---------- */
    // Rows can carry data-section="3-1" and data-date="2026-10-06".
    // Rows without those attributes are never hidden by that filter.
    const tbody = $("table tbody");
    if (tbody) {
        const rows = [...tbody.rows];
        const search = $("#student-search");
        const section = $("#sections");
        const date = $("#record-date");

        const emptyRow = tbody.insertRow();
        emptyRow.hidden = true;
        emptyRow.innerHTML = `<td colspan="${$("table thead tr").cells.length}">No matching records.</td>`;

        const apply = () => {
            const q = search.value.trim().toLowerCase();
            let shown = 0;
            rows.forEach((row) => {
                const match =
                    (!q || row.cells[0].textContent.toLowerCase().includes(q)) &&
                    (!section || !row.dataset.section || row.dataset.section === section.value) &&
                    (!date || !date.value || !row.dataset.date || row.dataset.date === date.value);
                row.hidden = !match;
                if (match) shown++;
            });
            emptyRow.hidden = shown > 0;
        };

        [search, section, date].forEach((el) => el && el.addEventListener("input", apply));
        $("#search-btn").addEventListener("click", apply);
        search.addEventListener("keydown", (e) => {
            if (e.key === "Enter") apply();
        });
        apply();
    }

    /* ---------- New session form (adminhome) ---------- */
    const form = $('form[action$="attendance-form.php"]');
    if (form) {
        const date = $("#session-date");
        const start = $("#start-time");
        const end = $("#end-time");
        const duration = $("#duration");

        [date, start, end, duration].forEach((el) => (el.required = true));

        const now = new Date();
        const today = [
            now.getFullYear(),
            String(now.getMonth() + 1).padStart(2, "0"),
            String(now.getDate()).padStart(2, "0"),
        ].join("-");
        date.value = today;
        date.min = today;

        const toMinutes = (t) => {
            const [h, m] = t.split(":").map(Number);
            return h * 60 + m;
        };

        const validate = () => {
            end.setCustomValidity("");
            duration.setCustomValidity("");
            if (!start.value || !end.value) return;
            const span = toMinutes(end.value) - toMinutes(start.value);
            if (span <= 0) {
                end.setCustomValidity("End time must be after start time.");
            } else if (duration.value && Number(duration.value) > span) {
                duration.setCustomValidity(`Duration can't be longer than the ${span}-minute session.`);
            }
        };

        [start, end, duration].forEach((el) => el.addEventListener("input", validate));
    }
})();
(() => {
    const $ = (sel, root = document) => root.querySelector(sel);

    /* ---------- Logout ---------- */
    // The logout button submits a real POST form; this only asks first.
    const logoutForm = $("#logout-form");
    if (logoutForm) {
        logoutForm.addEventListener("submit", (e) => {
            if (!confirm("Log out?")) e.preventDefault();
        });
    }

    /* ---------- Open New Session form ---------- */
    const form = $('form[action$="/professor/session"]');
    if (form) {
        const date = $("#date");
        const start = $("#started_at");
        const end = $("#ended_at");
        const deadline = $("#deadline");

        // Default the date to today and block past dates
        if (!date.value) {
            const now = new Date();
            date.value = [
                now.getFullYear(),
                String(now.getMonth() + 1).padStart(2, "0"),
                String(now.getDate()).padStart(2, "0"),
            ].join("-");
        }
        date.min = new Date().toLocaleDateString("en-CA"); // YYYY-MM-DD

        const validate = () => {
            end.setCustomValidity("");
            deadline.setCustomValidity("");
            if (start.value && end.value && end.value <= start.value) {
                end.setCustomValidity("End of class must be after the start of class.");
            }
            if (start.value && deadline.value && deadline.value < start.value) {
                deadline.setCustomValidity("Deadline can't be earlier than the start of class.");
            }
        };

        [start, end, deadline].forEach((el) => el.addEventListener("input", validate));
    }
    /* ---------- Keep the scroll position when filtering Attendance Records ---------- */
    const filterForm = $("form.record-filter-card");
    if (filterForm) {
        const KEY = "records-scroll";

        // Prevent the browser from restoring scroll itself
        if ("scrollRestoration" in history) {
            history.scrollRestoration = "manual";
        }

        // Runs for Section / Date / Session changes and for the search button
        const save = () => sessionStorage.setItem(KEY, String(window.scrollY));
        filterForm.addEventListener("change", save, true);
        filterForm.addEventListener("submit", save);

        const saved = sessionStorage.getItem(KEY);
        if (saved !== null) {
            sessionStorage.removeItem(KEY);
            const y = Number(saved);
            // Wait for the filtered content to be laid out before scrolling
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    window.scrollTo(0, y);
                });
            });
        }
    }

})();
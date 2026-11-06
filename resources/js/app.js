document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", (event) => {
        if (!form.checkValidity()) return;
        const button = event.submitter;
        if (!(button instanceof HTMLButtonElement)) return;
        if (button.name) {
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = button.name;
            hidden.value = button.value;
            form.appendChild(hidden);
        }
        button.disabled = true;
        button.dataset.original = button.textContent;
        button.textContent = "Memproses…";
    });
});
window.addEventListener("pageshow", () => {
    document.querySelectorAll("button[data-original]").forEach((button) => {
        button.disabled = false;
        button.textContent = button.dataset.original;
    });
});

document.querySelectorAll("[data-copy-target]").forEach((button) => {
    button.addEventListener("click", async () => {
        const code = document.getElementById(
            button.dataset.copyTarget,
        )?.textContent;
        const status = document.getElementById("copy-status");
        try {
            await navigator.clipboard.writeText(code || "");
            if (status) status.textContent = "Kode tersalin.";
        } catch {
            if (status)
                status.textContent =
                    "Salin kode secara manual dari tampilan di atas.";
        }
    });
});

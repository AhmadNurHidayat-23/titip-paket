import { test, expect } from "@playwright/test";
test("Penerimaan, foto privat, kode sekali pakai, pengambilan, dan laporan", async ({
    page,
    browser,
}) => {
    const errors: string[] = [];
    page.on("pageerror", (e) => errors.push(e.message));
    await page.goto("/");
    await expect(page).toHaveURL(/masuk/);
    await page
        .getByLabel("Email", { exact: true })
        .fill("petugas@example.test");
    await page
        .getByLabel("Kata sandi", { exact: true })
        .fill(process.env.E2E_PASSWORD!);
    await page.getByRole("button", { name: "Masuk ke pencatatan" }).click();
    await page
        .getByRole("link", { name: "Catat paket masuk", exact: false })
        .first()
        .click();
    await page.getByLabel("Nama penerima").fill("Penerima Browser");
    await page
        .getByLabel("Unit / identifikasi penerima (opsional)")
        .fill("Unit A-01");
    await page
        .getByLabel("Nomor resi / identifikasi paket")
        .fill("RESIBROWSER2026");
    await page.getByLabel("Kurir").selectOption("JNE");
    await page
        .getByLabel("Foto kondisi paket")
        .setInputFiles("tests/fixtures/package.jpg");
    await page
        .getByRole("button", { name: "Simpan dan buat kode pengambilan" })
        .click();
    await expect(page).toHaveURL(/paket\/[a-f0-9-]+$/);
    const item = new URL(page.url()).pathname;
    const code = (await page.locator("#pickup-code").innerText()).trim();
    expect(code).toMatch(/^TP-[A-Z0-9]{10}$/);
    await page
        .locator(".photo-panel img")
        .evaluate(async (img: HTMLImageElement) => img.decode());
    await page
        .getByLabel("Kode pengambilan", { exact: true })
        .fill("TP-WRONGCODE0");
    await page.getByLabel("Nama pengambil").fill("Penerima Browser");
    await page
        .getByLabel("Saya sudah memeriksa identitas dan kewenangan pengambil.")
        .check();
    await page
        .getByRole("button", { name: "Verifikasi dan serahkan paket" })
        .click();
    await expect(page.getByRole("alert")).toContainText("Kode tidak sesuai");
    await expect(page.locator("#pickup-code")).toHaveCount(0);
    await page.getByLabel("Kode pengambilan", { exact: true }).fill(code);
    await page.getByLabel("Nama pengambil").fill("Penerima Browser");
    await page
        .getByLabel("Saya sudah memeriksa identitas dan kewenangan pengambil.")
        .check();
    await page
        .getByRole("button", { name: "Verifikasi dan serahkan paket" })
        .click();
    await expect(
        page.getByRole("heading", { name: "Paket sudah diambil." }),
    ).toBeVisible();
    await expect(page.locator(".collection-panel")).toContainText(
        "Dinonaktifkan permanen",
    );
    await expect(
        page.getByLabel("Kode pengambilan", { exact: true }),
    ).toHaveCount(0);
    await page
        .getByRole("link", { name: "Aktivitas petugas", exact: true })
        .click();
    await expect(page.locator("tbody tr")).toHaveCount(2);
    const download = page.waitForEvent("download");
    await page.getByRole("link", { name: "Ekspor CSV" }).click();
    expect((await download).suggestedFilename()).toBe("aktivitas-paket.csv");
    for (const width of [1440, 768, 390]) {
        await page.setViewportSize({ width, height: 900 });
        await page.goto(item);
        await page
            .locator(".photo-panel img")
            .evaluate(async (img: HTMLImageElement) => img.decode());
        expect(
            await page.evaluate(
                () => document.documentElement.scrollWidth <= innerWidth + 1,
            ),
        ).toBe(true);
        await page.screenshot({
            path: "test-results/package-" + width + ".png",
            fullPage: true,
        });
        await page.goto("/");
        expect(
            await page.evaluate(
                () => document.documentElement.scrollWidth <= innerWidth + 1,
            ),
        ).toBe(true);
    }
    const guestContext = await browser.newContext();
    const guest = await guestContext.newPage();
    const response = await guest.goto(item + "/foto");
    expect(response?.url()).toContain("/masuk");
    expect(errors).toEqual([]);
    await guestContext.close();
});

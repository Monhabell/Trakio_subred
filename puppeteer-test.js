import puppeteer from 'puppeteer';

(async () => {
  const browser = await puppeteer.launch({
    executablePath: '/snap/bin/chromium', // Asegúrate de usar la ruta correcta de Chromium
    headless: true // Puedes cambiar esto a 'false' para ver lo que ocurre en el navegador
  });
  const page = await browser.newPage();
  await page.goto('https://google.com.co');
  await page.screenshot({ path: 'example.png' });
  await browser.close();
})();
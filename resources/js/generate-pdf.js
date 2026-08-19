import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

(async () => {    
    const args = process.argv.slice(2);
    const inputPath = args[0];
    const outputPath = args[1];

    if (!inputPath || !outputPath) {
        console.error('Por favor, proporciona las rutas de entrada y salida.');
        process.exit(1);
    }

    // Define el directorio temporal relativo a la raíz del proyecto
    const tempDir = path.join(__dirname, '../storage/app/public/acts/puppeteer_temp');

    // Crea el directorio si no existe
    if (!fs.existsSync(tempDir)) {
        fs.mkdirSync(tempDir, { recursive: true }); // Asegúrate de que se creen directorios padre si es necesario
    }

    try {
        const browser = await puppeteer.launch({
            userDataDir: tempDir
        });
        
        const page = await browser.newPage();
        const htmlContent = fs.readFileSync(inputPath, 'utf8');

        await page.setContent(htmlContent, { waitUntil: 'networkidle0' });
        await page.pdf({ 
            path: outputPath, 
            format: 'A4' 
        });

        await browser.close();
    } catch (error) {
        console.error('Error al generar el PDF:', error);
        process.exit(1);
    }
})();
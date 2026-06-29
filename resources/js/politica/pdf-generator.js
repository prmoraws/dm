// resources/js/politica/pdf-generator.js

import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

// Função que será chamada pelo botão na nossa view
export default async function generatePdf(cityName) {
    // 1. Seleciona o elemento que queremos imprimir
    const reportContent = document.getElementById('report-content');
    if (!reportContent) {
        console.error('Elemento #report-content não encontrado!');
        return;
    }

    // 2. Usa html2canvas para criar uma imagem do elemento
    // A opção 'scale' melhora a resolução da imagem final
    const canvas = await html2canvas(reportContent, { scale: 2 });

    // 3. Pega as dimensões da imagem e da página PDF (A4)
    const imgData = canvas.toDataURL('image/png');
    const imgWidth = 210; // Largura de uma página A4 em mm
    const pageHeight = 295; // Altura de uma página A4 em mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    let heightLeft = imgHeight;

    // 4. Cria o documento PDF
    const pdf = new jsPDF('p', 'mm', 'a4');
    let position = 0;

    // 5. Adiciona a imagem ao PDF. Se for muito grande, cria várias páginas
    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft >= 0) {
        position = heightLeft - imgHeight;
        pdf.addPage();
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }

    // 6. Salva o arquivo com um nome dinâmico
    pdf.save(`espelho-${cityName.toLowerCase().replace(/ /g, '_')}.pdf`);
}
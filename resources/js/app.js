import './bootstrap';
import Chart from 'chart.js/auto';
window.Chart = Chart;

import generatePdf from './politica/pdf-generator.js';
window.generatePdf = generatePdf; // Torna a função acessível globalmente para o Alpine.js
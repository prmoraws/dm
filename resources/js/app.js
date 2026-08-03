import './bootstrap';
import Chart from 'chart.js/auto';
window.Chart = Chart;

import generatePdf from './politica/pdf-generator.js';
window.generatePdf = generatePdf; // Torna a função acessível globalmente para o Alpine.js

function atualizarIconeDoTema(botao) {
    const escuro = document.documentElement.classList.contains('dark');
    botao.querySelector('[data-theme-icon="light"]')?.classList.toggle('hidden', escuro);
    botao.querySelector('[data-theme-icon="dark"]')?.classList.toggle('hidden', !escuro);
    botao.setAttribute('aria-pressed', escuro ? 'true' : 'false');
}

document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('theme-toggle');
    if (!botao) return;

    atualizarIconeDoTema(botao);
    botao.addEventListener('click', () => {
        const escuro = !document.documentElement.classList.contains('dark');
        document.documentElement.classList.toggle('dark', escuro);
        localStorage.setItem('darkMode', escuro ? 'true' : 'false');
        atualizarIconeDoTema(botao);
    });
});

<style>
    .anexo-print-pages { display: block; width: 210mm; max-width: none; margin: 0 auto; }
    .anexo-print-page {
        box-sizing: border-box; display: block; width: 210mm; min-height: 297mm;
        margin: 0 auto 16px; padding: 10mm; background: #fff; color: #000;
        font-family: Arial, Helvetica, sans-serif; font-size: 10pt; line-height: 1.3;
        box-shadow: 0 5px 20px rgba(0,0,0,.15); overflow: visible;
    }
    .anexo-print-header { display: block; height: 16mm; text-align: right; border-bottom: 3px solid #172554; }
    .anexo-print-header img { display: inline-block; width: 40mm; height: 14mm; object-fit: contain; }
    .anexo-print-coordenadoria { margin: 2mm 0 4mm; text-align: center; font-size: 11pt; font-weight: bold; }
    .anexo-print-main { display: block; min-height: 218mm; overflow: visible; }
    .anexo-print-title { margin: 0 0 5mm; font-size: 12pt; line-height: 1.25; font-weight: bold; text-align: center; text-decoration: underline; overflow-wrap: anywhere; }
    .anexo-print-section { display: block; margin: 0 0 3mm; }
    .anexo-print-category { margin: 0 0 2mm; font-size: 11pt; line-height: 1.2; font-weight: bold; text-decoration: underline; }
    .anexo-print-name { display: block; margin: 0 0 1mm; font-size: 10pt; line-height: 1.3; overflow-wrap: anywhere; word-wrap: break-word; }
    .anexo-print-footer { display: block; margin-top: 3mm; border-top: 3px solid #172554; padding-top: 1mm; text-align: center; font-size: 8pt; line-height: 1.2; }
    .anexo-print-footer p { margin: 1mm 0; }
    .anexo-print-banner { font-size: 12pt; font-weight: bold; color: #172554; }
    .anexo-print-banner span { color: #dc2626; }
    .anexo-print-number { font-size: 8pt; }
    .anexo-preview-scroll { display: block; width: 100%; overflow-x: auto; }
    #anexo-print-root { display: none; }
    @page { size: A4 portrait; margin: 10mm; }
    @media print {
        html, body { background: #fff !important; height: auto !important; min-height: 0 !important; margin: 0 !important; padding: 0 !important; overflow: visible !important; }
        body.anexo-printing > :not(#anexo-print-root) { display: none !important; }
        body.anexo-printing > #anexo-print-root { display: block !important; }
        #anexo-print-root, #anexo-print-root * { visibility: visible !important; }
        .anexo-print-controls { display: none !important; }
        .anexo-print-pages { width: auto !important; margin: 0 !important; }
        .anexo-print-page {
            display: block !important; width: auto !important; min-height: 0 !important;
            height: auto !important; margin: 0 !important; padding: 0 !important;
            border: 0 !important; box-shadow: none !important; overflow: visible !important;
            page-break-inside: auto; break-inside: auto;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .anexo-print-page + .anexo-print-page { page-break-before: always; break-before: page; }
        .anexo-print-title, .anexo-print-category { page-break-after: avoid; break-after: avoid-page; }
        .anexo-print-name, .anexo-print-header, .anexo-print-footer { page-break-inside: avoid; break-inside: avoid-page; }
        .anexo-print-main { height: auto !important; overflow: visible !important; }
    }
</style>

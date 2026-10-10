<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anexo de Ofício</title>
    @include('livewire.unp.oficios.partials.anexo-print-styles')
    <style>
        body { margin: 0; padding: 16px; background: #eceff3; }
        .anexo-print-controls { text-align: center; margin: 0 0 16px; font-family: Arial, sans-serif; }
        .anexo-print-controls button { border: 0; border-radius: 8px; padding: 12px 20px; background: #2563eb; color: white; font-size: 16px; cursor: pointer; }
        .anexo-print-controls p { font-size: 14px; }
    </style>
</head>
<body>
    <div class="anexo-print-controls">
        <button type="button" onclick="printOficio()">Imprimir / salvar como PDF</button>
        <p>Também é possível imprimir pelo menu do navegador.</p>
    </div>
    <div id="oficio-para-impressao" class="anexo-print-pages">
        @include('livewire.unp.oficios.partials.anexo-print-pages')
    </div>
    @include('livewire.unp.oficios.partials.anexo-print-script', ['autoPrintAnexo' => true])
</body>
</html>

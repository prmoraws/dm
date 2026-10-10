<script>
    // Imprime uma cópia fora do modal; os ancestrais fixos não limitam a paginação.
    window.prepareAnexoPrint = function () {
        const source = document.getElementById('oficio-para-impressao');
        if (!source) return;
        let root = document.getElementById('anexo-print-root');
        if (!root) {
            root = document.createElement('div');
            root.id = 'anexo-print-root';
            document.body.appendChild(root);
        }
        root.replaceChildren(...Array.from(source.childNodes, node => node.cloneNode(true)));
        document.body.classList.add('anexo-printing');
    };
    window.cleanupAnexoPrint = function () {
        document.body.classList.remove('anexo-printing');
        const root = document.getElementById('anexo-print-root');
        if (root) root.remove();
    };
    window.printOficio = function () {
        window.prepareAnexoPrint();
        window.print();
    };
    if (!window.anexoPrintHooksInstalled) {
        window.addEventListener('beforeprint', function () { window.prepareAnexoPrint(); });
        window.addEventListener('afterprint', function () { window.cleanupAnexoPrint(); });
        window.anexoPrintHooksInstalled = true;
    }
    @if($autoPrintAnexo ?? false)
        if (document.readyState === 'complete') {
            window.printOficio();
        } else {
            window.addEventListener('load', function () { window.printOficio(); }, { once: true });
        }
    @endif
</script>

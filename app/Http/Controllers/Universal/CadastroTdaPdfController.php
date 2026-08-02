<?php
namespace App\Http\Controllers\Universal;
use App\Http\Controllers\Controller;
use App\Models\Universal\CadastroTda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
class CadastroTdaPdfController extends Controller
{
    use AuthorizesRequests;
    public function visualizar(CadastroTda $cadastroTda)
    {
        $this->authorize('view',$cadastroTda);
        return $this->pdf($cadastroTda)->stream('ficha-tda-'.$cadastroTda->id.'.pdf');
    }
    public function baixar(CadastroTda $cadastroTda)
    {
        $this->authorize('view',$cadastroTda);
        return $this->pdf($cadastroTda)->download('ficha-tda-'.$cadastroTda->id.'.pdf');
    }
    private function pdf(CadastroTda $cadastroTda)
    {
        $cadastroTda->load(['bloco','regiao','igreja','estado','cidade']);
        $fotoDataUri=null;
        $disk=Storage::disk('public_disk');
        if($cadastroTda->foto && $disk->exists($cadastroTda->foto)){
            $mime=$disk->mimeType($cadastroTda->foto) ?: 'image/jpeg';
            $fotoDataUri='data:'.$mime.';base64,'.base64_encode($disk->get($cadastroTda->foto));
        }
        $pagina1DataUri=$this->imagemPublicaDataUri('images/tda/ficha-base-pagina-1.png');
        $pagina2DataUri=$this->imagemPublicaDataUri('images/tda/ficha-base-pagina-2.png');
        return Pdf::loadView('livewire.universal.pdf.cadastro-tda',[
            'pessoa'=>$cadastroTda,
            'fotoDataUri'=>$fotoDataUri,
            'pagina1DataUri'=>$pagina1DataUri,
            'pagina2DataUri'=>$pagina2DataUri,
        ])->setPaper('a4','portrait');
    }

    private function imagemPublicaDataUri(string $caminho): ?string
    {
        $arquivo=public_path($caminho);
        if(!is_file($arquivo)) return null;
        $mime=mime_content_type($arquivo) ?: 'image/png';
        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($arquivo));
    }
}

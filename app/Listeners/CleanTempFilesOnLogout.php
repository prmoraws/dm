<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanTempFilesOnLogout
{
    public function handle(Logout $event)
    {
        try {
            $tempDir = Storage::disk('public')->path('temp');
            $tempDir = str_replace('\\', '/', $tempDir);
            if (file_exists($tempDir)) {
                $files = glob($tempDir . '/*.{xlsx,pdf}', GLOB_BRACE);
                foreach ($files as $file) {
                    unlink($file);
                    Log::channel('dashboard')->info('Arquivo temporário removido no logout', ['file' => $file]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('dashboard')->error('Erro ao limpar arquivos temporários no logout', ['exception' => $e->getMessage()]);
        }
    }
}

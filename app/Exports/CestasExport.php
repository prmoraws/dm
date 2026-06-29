<?php

namespace App\Exports;

use App\Models\Evento\Cesta;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CestasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $cestas;

    public function __construct($cestas)
    {
        $this->cestas = $cestas;
    }

    public function collection()
    {
        return $this->cestas;
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Instituição',
            'Cestas',
            'Observação',
            'Foto',
            'Criado Em',
        ];
    }

    public function map($cesta): array
    {
        return [
            $cesta->nome,
            $cesta->terreiro,
            $cesta->cestas,
            $cesta->observacao ?? 'Nenhuma',
            $cesta->foto ? Storage::disk('public')->url($cesta->foto) : 'Nenhuma',
            $cesta->created_at->format('d/m/Y H:i'),
        ];
    }
}

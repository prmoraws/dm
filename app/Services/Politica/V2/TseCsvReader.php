<?php

namespace App\Services\Politica\V2;

use Generator;
use RuntimeException;
use ZipArchive;

class TseCsvReader
{
    /**
     * @return Generator<int,array<string,string|null>>
     */
    public function rows(string $path, ?callable $entrySelector = null): Generator
    {
        if (! is_file($path)) {
            throw new RuntimeException("Arquivo TSE não encontrado: {$path}");
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'csv') {
            yield from $this->rowsFromStream($this->openFile($path));
            return;
        }

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('A extensão PHP ZipArchive é necessária para ler os arquivos ZIP do TSE.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException("Não foi possível abrir o ZIP do TSE: {$path}");
        }

        try {
            $selected = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = (string) $zip->getNameIndex($i);
                if (! str_ends_with(strtolower($name), '.csv')) {
                    continue;
                }
                if ($entrySelector === null || $entrySelector($name)) {
                    $selected[] = $name;
                }
            }

            if ($selected === []) {
                throw new RuntimeException('Nenhum CSV compatível foi encontrado dentro do arquivo do TSE.');
            }

            foreach ($selected as $name) {
                $stream = $zip->getStream($name);
                if (! is_resource($stream)) {
                    throw new RuntimeException("Não foi possível abrir {$name} dentro do ZIP.");
                }

                try {
                    yield from $this->rowsFromStream($stream);
                } finally {
                    fclose($stream);
                }
            }
        } finally {
            $zip->close();
        }
    }

    /** @return resource */
    private function openFile(string $path)
    {
        $stream = fopen($path, 'rb');
        if (! is_resource($stream)) {
            throw new RuntimeException("Não foi possível abrir o CSV: {$path}");
        }

        return $stream;
    }

    /**
     * @param resource $stream
     * @return Generator<int,array<string,string|null>>
     */
    private function rowsFromStream($stream): Generator
    {
        $headerRaw = fgetcsv($stream, 0, ';', '"', '\\');
        if (! is_array($headerRaw)) {
            return;
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $headerRaw);
        $expected = count($headers);

        while (($values = fgetcsv($stream, 0, ';', '"', '\\')) !== false) {
            if ($values === [null] || $values === []) {
                continue;
            }

            if (count($values) < $expected) {
                $values = array_pad($values, $expected, null);
            } elseif (count($values) > $expected) {
                $values = array_slice($values, 0, $expected);
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $value = $values[$index] ?? null;
                $row[$header] = $value === null ? null : $this->toUtf8((string) $value);
            }

            yield $row;
        }
    }

    private function normalizeHeader(string $value): string
    {
        $value = $this->toUtf8($value);
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;

        return strtoupper(trim($value, "\xEF\xBB\xBF\" \t\r\n"));
    }

    private function toUtf8(string $value): string
    {
        if ($value === '' || mb_check_encoding($value, 'UTF-8')) {
            return trim($value);
        }

        return trim(mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1'));
    }
}

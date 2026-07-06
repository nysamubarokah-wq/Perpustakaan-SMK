<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportTemplateService
{
    public static function templates(): array
    {
        return [
            'buku' => [
                'filename' => 'template_import_buku.csv',
                'columns' => ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'stok', 'genre', 'lokasi', 'deskripsi', 'url_sampul'],
                'example' => [
                    'Matematika Pemrograman',
                    'Budi Santoso',
                    'Erlangga',
                    '2024',
                    '9786021000001',
                    '10',
                    'Sains & Teknologi',
                    'A1',
                    'Buku panduan matematika untuk siswa SMA',
                    'https://example.com/sampul.jpg',
                ],
            ],
            'anggota' => [
                'filename' => 'template_import_anggota.csv',
                'columns' => ['nis', 'nama', 'kelas', 'jurusan', 'jk', 'no_hp', 'email'],
                'example' => [
                    '2024001',
                    'Anisa Rahmawati',
                    'X RPL 1',
                    'RPL',
                    'L',
                    '081234567890',
                    'anisa@siswa.sch.id',
                ],
            ],
        ];
    }

    public static function download(string $type): ?StreamedResponse
    {
        $templates = self::templates();
        if (!isset($templates[$type])) {
            return null;
        }

        $template = $templates[$type];
        $columns = $template['columns'];
        $example = $template['example'];
        $filename = $template['filename'];

        $response = new StreamedResponse(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    public static function getColumns(string $type): ?array
    {
        return self::templates()[$type]['columns'] ?? null;
    }
}

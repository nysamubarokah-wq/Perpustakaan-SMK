<?php

namespace App\Http\Controllers;

use App\Services\ImportTemplateService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportTemplateController extends Controller
{
    public function download(string $type): ?StreamedResponse
    {
        return ImportTemplateService::download($type);
    }
}

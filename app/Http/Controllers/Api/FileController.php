<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function uploadFile(UploadFileRequest $request)
    {
        $file = $request->hasFile('file') ? $request->file('file') : null;
        if (!$file) {
            throw new \Exception('Файл не найден', ErrorCodes::VALIDATION_ERROR);
        }

        $file_extension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $file_extension;
        Storage::disk('uploads')->put($fileName, \File::get($file));
        $mimeType = Storage::disk('uploads')->mimeType($fileName);
        $url = Storage::disk('uploads')->url($fileName);

        return response()->json([
            'mimeType' => $mimeType,
            'url' => $url,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(UploadRequest $request)
    {
        $result = $this->uploadService->handleUpload(
            $request->file('files'),
            $request->input('expires_in', 1),
            $request->input('email_to_notify'),
            $request->input('password')
        );

        return response()->json([
            'success' => true,
            'download_link' => route('download', ['token' => $result['token']])
        ]);
    }

    public function download(Request $request, $token)
    {
        $password = $request->input('password');
        
        try {
            $file = $this->uploadService->prepareDownload($token, $password);
            
            return response()->stream(function () use ($file) {
                $stream = fopen($file['path'], 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type' => $file['mime_type'],
                'Content-Disposition' => 'attachment; filename="' . $file['name'] . '"',
                'Content-Length' => $file['size'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function stats($token)
    {
        try {
            $stats = $this->uploadService->getStats($token);
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}

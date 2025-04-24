<?php

namespace App\Services;

use App\Models\UploadFile;
use App\Models\UploadSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\UploadNotification;

class UploadService
{
    public function handleUpload(array $files, int $expiresIn = 1, ?string $emailToNotify = null, ?string $password = null)
    {
        // Create upload session
        $uploadSession = UploadSession::create([
            'expires_at' => Carbon::now()->addDays($expiresIn),
            'email_to_notify' => $emailToNotify,
            'password' => $password ? Hash::make($password) : null,
        ]);

        // Store each file
        foreach ($files as $file) {
            $path = Storage::disk('local')->put('uploads/' . $uploadSession->token, $file);
            
            UploadFile::create([
                'upload_session_id' => $uploadSession->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        // Send email notification if requested
        if ($emailToNotify) {
            $this->sendNotificationEmail($uploadSession, $emailToNotify);
        }

        return [
            'token' => $uploadSession->token,
        ];
    }

    public function prepareDownload(string $token, ?string $password = null)
    {
        $uploadSession = UploadSession::where('token', $token)->first();

        if (!$uploadSession) {
            throw new \Exception('Upload not found.');
        }

        if ($uploadSession->isExpired()) {
            throw new \Exception('This upload has expired.');
        }

        if ($uploadSession->password && !Hash::check($password, $uploadSession->password)) {
            throw new \Exception('Invalid password.');
        }

        // Get the first file (for simplicity, we'll return the first file)
        // In a real app, you might want to handle multiple files differently
        $file = $uploadSession->files()->first();

        if (!$file) {
            throw new \Exception('No files found in this upload.');
        }

        // Increment download count
        $uploadSession->incrementDownloadCount();

        return [
            'path' => Storage::disk('local')->path($file->file_path),
            'name' => $file->original_name,
            'size' => $file->file_size,
            'mime_type' => $file->mime_type,
        ];
    }

    public function getStats(string $token)
    {
        $uploadSession = UploadSession::where('token', $token)->first();

        if (!$uploadSession) {
            throw new \Exception('Upload not found.');
        }

        $totalSize = $uploadSession->files->sum('file_size');
        $fileCount = $uploadSession->files->count();

        return [
            'token' => $uploadSession->token,
            'created_at' => $uploadSession->created_at->toDateTimeString(),
            'expires_at' => $uploadSession->expires_at->toDateTimeString(),
            'expires_in_hours' => max(0, Carbon::now()->diffInHours($uploadSession->expires_at)),
            'download_count' => $uploadSession->download_count,
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'files' => $uploadSession->files->map(function ($file) {
                return [
                    'name' => $file->original_name,
                    'size' => $this->formatBytes($file->file_size),
                    'mime_type' => $file->mime_type,
                ];
            }),
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function sendNotificationEmail(UploadSession $uploadSession, string $email)
    {
        // In a real application, you would use a proper email template
        // and queue the email sending process
        $downloadLink = route('download', ['token' => $uploadSession->token]);
        $expiresAt = $uploadSession->expires_at->format('Y-m-d H:i:s');
        
        // This would typically be queued
        Mail::raw(
            "Your files have been uploaded successfully.\n\n" .
            "Download link: {$downloadLink}\n" .
            "Expires at: {$expiresAt}\n" .
            ($uploadSession->password ? "This upload is password protected." : ""),
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your files have been uploaded');
            }
        );
    }
}

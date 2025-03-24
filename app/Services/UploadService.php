<?php

namespace App\Services;

use App\Models\UploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * simple upload when file size is lower than chunk size.
     *
     * @param UploadedFile $file $file
     * @return string
     */
    public function simpleUpload(UploadedFile $file): string
    {
        $folder = uniqid(). '-' . now()->timestamp;
        $file->storePubliclyAs('tmp/'. $folder, $file->getClientOriginalName(), 'public');

        UploadFile::query()->create([
            'name' => $folder,
            'user_id' => auth()->id(),
        ]);

        return $folder;
    }

    /**
     * starting chunk upload.
     *
     * @return string|null
     */
    public function startChunkProcess(): ?string
    {
        $maxAttempts = 5;
        $attempt = 0;

        do {
            $folderName = Str::random(10);
            $exists = Storage::disk('public')->exists("tmp/chunk/$folderName");
            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        if ($attempt >= $maxAttempts) {
            return null;
        }

        Storage::disk('public')->makeDirectory("tmp/chunk/$folderName");

        UploadFile::query()->create([
            'name' => $folderName,
            'user_id' => auth()->id(),
        ]);

        return $folderName;
    }

    /**
     * process chunk uploads.
     *
     * @param Request $request
     * @return bool
     */
    public function processChunkUploads(Request $request): bool
    {
        $offset = (float)$request->header('Upload-Offset');
        $length = (float)$request->header('Upload-Length');
        $filename = $request->header('Upload-Name');
        $folderName = trim($request->input('patch'), '"');
        $pathName = "tmp/chunk/".$folderName;

        if (!Storage::disk('public')->exists($pathName)) return false;
        $chunkFolder = Storage::disk('public')->path($pathName);

        $chunk = $request->getContent();
        Storage::disk('public')->put($pathName."/chunk_".$offset, $chunk);

        if ($offset + mb_strlen($chunk, '8bit') >= $length) {
            $this->combineChunks($chunkFolder, $filename, $folderName);
        }
        return true;
    }

    /**
     * when chunk process finishes it combines chunk files
     *
     * @param string $chunkFolder
     * @param string $filename
     * @param string $unique_id
     * @return void
     */
    public function combineChunks(string $chunkFolder, string $filename, string $unique_id): void
    {
        $finalFile = 'tmp/' . $unique_id . '/' . $filename;
        $directoryPath = dirname($finalFile);

        Storage::disk('public')->makeDirectory($directoryPath);

        $finalPath = Storage::disk('public')->path($finalFile);
        $finalFileHandle  = fopen($finalPath, 'wb');

        $chunkFiles = glob($chunkFolder . '/chunk_*');
        usort($chunkFiles, function($a, $b) {
            return (int) substr($a, strrpos($a, '_') + 1) - (int) substr($b, strrpos($b, '_') + 1);
        });

        foreach ($chunkFiles as $chunkFile) {
            $chunkContent = file_get_contents($chunkFile);
            fwrite($finalFileHandle, $chunkContent);
        }

        fclose($finalFileHandle);

        array_map('unlink', glob($chunkFolder . '/chunk_*'));
        rmdir($chunkFolder);
    }
}

<?php

namespace App\Services;

use ImageKit\ImageKit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageKitService
{
    protected $imageKit;

    public function __construct()
    {
        $this->imageKit = new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }

    /**
     * Upload an image to ImageKit.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return object|null
     */
    public function upload($file, $folder = 'uploads')
    {
        try {
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            $upload = $this->imageKit->uploadFile([
                'file' => base64_encode(file_get_contents($file->getRealPath())),
                'fileName' => $fileName,
                'folder' => $folder,
                'useUniqueFileName' => true,
            ]);

            if ($upload->error) {
                Log::error('ImageKit Upload Error: ' . json_encode($upload->error));
                return null;
            }

            return $upload->result;
        } catch (\Exception $e) {
            Log::error('ImageKit Upload Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image from ImageKit.
     *
     * @param string $fileId
     * @return bool
     */
    public function delete($fileId)
    {
        if (!$fileId) return true;

        try {
            $delete = $this->imageKit->deleteFile($fileId);
            
            if ($delete->error) {
                Log::error('ImageKit Delete Error: ' . json_encode($delete->error));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('ImageKit Delete Exception: ' . $e->getMessage());
            return false;
        }
    }
}

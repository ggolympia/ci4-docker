<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

class ImageController extends BaseController
{
    private $directories = [
        WRITEPATH . '../public/images/',
        WRITEPATH . '../public/uploads/',
    ];

    /**
     * Fetch an image by name from any subdirectory.
     */
    public function fetchImage($imageName)
    {
        // log_message('debug', 'Searching for image: ' . $imageName);

        // Decode the URL to handle spaces and special characters
        $decodedName = urldecode($imageName);
        // log_message('debug', 'Decoded image name: ' . $decodedName);

        // Attempt to find the file across the directory structure
        foreach ($this->directories as $directory) {
            $filePath = $this->findFile($decodedName, $directory);
            if ($filePath) {
                // log_message('debug', 'Image found at: ' . $filePath);
                $mimeType = mime_content_type($filePath);
                return $this->response
                    ->setContentType($mimeType)
                    ->setBody(file_get_contents($filePath));
            }
        }

        // log_message('error', 'Image not found: ' . $decodedName);
        return $this->response->setStatusCode(404, 'Image not found');
    }

    /**
     * Recursively search for a file by name within the specified directory.
     */
    private function findFile($fileName, $directory)
    {
        // log_message('debug', 'Searching in directory: ' . $directory);

        // Search the current directory and its subdirectories
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_file($fullPath) && basename($fullPath) === $fileName) {
                // log_message('debug', 'File match found: ' . $fullPath);
                return $fullPath;
            }

            if (is_dir($fullPath)) {
                $result = $this->findFile($fileName, $fullPath);
                if ($result) {
                    return $result;
                }
            }
        }

        return false;
    }
}

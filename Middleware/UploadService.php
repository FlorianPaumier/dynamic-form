<?php


namespace App\Service;


use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{
    private $slugger;
    /**
     * @var FilesystemOperator
     */
    private FilesystemOperator $filesystemOperator;

    public function __construct(SluggerInterface $slugger, FilesystemOperator $defaultStorage)
    {
        $this->slugger = $slugger;
        $this->filesystemOperator = $defaultStorage;
    }

    public function uploadFile(UploadedFile $file, $path)
    {
        $extension = ["pdf", "png", "jpeg", "jpg"];

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($file && in_array($file->guessExtension(), $extension)) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $path,
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return $newFilename;
        }

        return null;
    }

    public function downloadsStream(string $path)
    {
        return new StreamedResponse(function () use ($path) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $this->filesystemOperator->readStream($path);
            stream_copy_to_stream($fileStream, $outputStream);
        });
    }

    public function downloads(string $path, string $filename)
    {
        // This should return the file to the browser as response
        $response = new BinaryFileResponse($path.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isGuesserSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($path.$filename));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }


}

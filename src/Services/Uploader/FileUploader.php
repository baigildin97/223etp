<?php
declare(strict_types=1);
namespace App\Services\Uploader;


use App\Services\Hash\Streebog;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileUploader
{

    /**
     * @var FilesystemInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * FileUploader constructor.
     * @param FilesystemInterface $storage
     * @param string $baseUrl
     * @param string $rootDirectory
     * @param string $algorithm
     */
    public function __construct(
        FilesystemInterface $storage,
        string $baseUrl,
        string $rootDirectory,
        string $algorithm
    ) {
        $this->storage = $storage;
        $this->baseUrl = $baseUrl;
        $this->rootDirectory = $rootDirectory;
        $this->algorithm = $algorithm;
    }

    /**
     * @param UploadedFile $file
     * @param bool $required_hashing
     * @param bool $brick_document
     * @return File
     * @throws FileExistsException
     */
    public function upload(UploadedFile $file, bool $required_hashing = false, bool $brick_document = false): File{
        $path = $this->rootDirectory.date('Y/m/d');
        $name = Uuid::uuid4()->toString(). '.' . $file->getClientOriginalExtension();

        $this->storage->createDir($path);

        $stream = fopen($file->getRealPath(), 'rb+');

        if ($brick_document) {
            $fcontent = fread($stream, $size = fstat($stream)['size']);
            $part = substr($fcontent, 0, 64);
            $fcontent = base64_decode(substr_replace(base64_encode($fcontent), hash('sha256', $part), 0));

            rewind($stream);
            fwrite($stream, $fcontent);
            fflush($stream);
            ftruncate($stream, $size);
        }


        $this->storage->writeStream($path . '/' . $name, $stream);
        fclose($stream);

        $hash = $required_hashing ? Streebog::getFileHash($this->generateUrl($path) . '/' . $name) : null;

        return new File($path, $name, $file->getSize(), $file->getClientOriginalName(), $hash);
    }


    /**
     * @param \App\Model\Work\Procedure\Entity\Document\File $file
     * @param bool $required_hashing
     * @return File
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function copyFile(
        \App\Model\Work\Procedure\Entity\Document\File $file,
        bool $required_hashing = false
    ): File{

        $path = $this->rootDirectory.date('Y/m/d');
        $extension = explode('.', $file->getName());
        $name = Uuid::uuid4()->toString(). '.' . $extension[1];
        $this->storage->createDir($path);

        $newPath = $path.'/'.$name;
        $this->storage->copy($file->getPath().'/'.$file->getName(), $newPath);

        $hash = $required_hashing ? Streebog::getFileHash($this->generateUrl($path) . '/' . $name) : null;

        return new File($newPath, $name, $file->getSize(), $file->getName(), $hash);
    }

    /**
     * @param string $path
     * @return string
     */
    public function generateUrl(string $path): string {
        return $this->baseUrl . '/' . $path;
    }
}

<?php

namespace Ngekoding\PdfMerger;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfMerger
{
    private $gsPath = 'gs';
    private $compressionLevel = CompressionLevel::DEFAULT;
    private $inputFiles = [];
    private $outputFile = null;
    private $outputFolder = null;
    private $outputFilename = null;
    private $timeout = 60;

    public function __construct()
    {
        $this->outputFolder = sys_get_temp_dir();
    }

    /**
     * Set the Ghostscript binary path (default: "gs").
     *
     * @param string $path
     * @return $this
     */
    public function setGsPath($path)
    {
        $this->gsPath = $path;

        return $this;
    }

    /**
     * Reset all internal states.
     *
     * @return $this
     */
    public function reset()
    {
        $this->compressionLevel = CompressionLevel::DEFAULT;
        $this->inputFiles = [];
        $this->outputFile = null;
        $this->outputFolder = sys_get_temp_dir();
        $this->outputFilename = null;
        $this->timeout = 60;

        return $this;
    }

    /**
     * Sets the process timeout (max. runtime) in seconds.
     * 
     * To disable the timeout, set this value to null.
     *
     * docs: https://symfony.com/doc/3.x/components/process.html#process-timeout
     * 
     * @param int|float|null $timeout The timeout in seconds
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets the compression level.
     *
     * @param string $level
     * 
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCompressionLevel($level)
    {   
        if (!in_array($level, CompressionLevel::all(), true)) {
            throw new \InvalidArgumentException("Invalid compression level: $level");
        }

        $this->compressionLevel = $level;
        
        return $this;
    }

    /**
     * Adds a single PDF file to the list of files to merge.
     *
     * @param string $file Full path to the PDF file.
     * 
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addFile($file)
    {
        if ( ! file_exists($file)) {
            throw new \InvalidArgumentException("File not found: $file");
        }

        $this->inputFiles[] = $file;
        
        return $this;
    }

    /**
     * Adds multiple PDF files to the list of files to merge.
     *
     * @param array $files Array of file paths.
     * 
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addFiles($files)
    {
        if ( ! is_array($files)) {
            throw new \InvalidArgumentException('addFiles expects an array of file paths.');
        }

        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Sets the full output file path (including file name).
     *
     * @param string $file
     * 
     * @return $this
     */
    public function setOutputFile($file)
    {
        $this->outputFile = $file;

        return $this;
    }

    /**
     * Sets the output folder where the merged file will be saved.
     *
     * @param string $folder
     * 
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setOutputFolder($folder)
    {
        if ( ! is_dir($folder)) {
            throw new \InvalidArgumentException("Folder does not exist: $folder");
        }
        
        $this->outputFolder = rtrim($folder, '/\\');

        return $this;
    }

    /**
     * Sets the name of the output file (without the folder path).
     *
     * @param string $filename
     * 
     * @return $this
     */
    public function setOutputFilename($filename)
    {
        $this->outputFilename = $filename;

        return $this;
    }

    /**
     * Build the Ghostscript command array.
     *
     * @param $finalOutputFile The fully resolved path for the output PDF file.
     * 
     * @return array
     */
    private function buildCommand($finalOutputFile)
    {
        $baseOptions = [
            $this->gsPath,
            '-dBATCH',
            '-dNOPAUSE',
            '-q',
            '-sDEVICE=pdfwrite',
        ];

        $compressionOption = [];
        if ($this->compressionLevel !== CompressionLevel::NONE) {
            $compressionOption = ['-dPDFSETTINGS=' . $this->compressionLevel];
        }

        $outputOption = ['-sOutputFile=' . $finalOutputFile];

        return array_merge(
            $baseOptions,
            $compressionOption,
            $outputOption,
            $this->inputFiles
        );
    }

    /**
     * Merges the input PDF files into a single output file.
     *
     * @return string The path to the merged PDF file.
     * @throws \RuntimeException
     * @throws ProcessFailedException
     */
    public function merge()
    {
        if (count($this->inputFiles) < 2) {
            throw new \RuntimeException('At least two PDF files are required for merging.');
        }

        $finalOutputFile = $this->outputFile;

        if ( ! $finalOutputFile) {
            $filename = !empty($this->outputFilename)
                ? $this->outputFilename
                : ('merged_' . date('Ymd_His') . '.pdf');

            $finalOutputFile = $this->outputFolder . DIRECTORY_SEPARATOR . $filename;
        }

        $command = $this->buildCommand($finalOutputFile);

        $process = new Process($command);
        $process->setTimeout($this->timeout);
        $process->run();

        if ( ! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if ( ! file_exists($finalOutputFile)) {
            throw new \RuntimeException("Merging failed: Output file not created.");
        }

        return $finalOutputFile;
    }
}

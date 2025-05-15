# pdf-merger

Merge multiple PDF files into a single PDF using Ghostscript.

This library uses the Ghostscript CLI to efficiently merge PDF files on Linux or any environment where Ghostscript is installed.

## Requirements

- PHP 5.6 or above  
- Ghostscript CLI (`gs` command)

## Installation

```bash
composer require ngekoding/pdf-merger
```

## Usage

### Simple usage

```php
<?php

require 'vendor/autoload.php';

use Ngekoding\PdfMerger\PdfMerger;

$merger = new PdfMerger();
$merger->addFiles([
    'file1.pdf',
    'file2.pdf',
    'file3.pdf'
]);

$output = $merger->merge();

echo "Merged PDF created at: $output\n";
```

### Adding files one by one

```php
$merger = new PdfMerger();
$merger->addFile('file1.pdf');
$merger->addFile('file2.pdf');
$output = $merger->merge();
```

### Setting custom output folder and filename

By default, the merged PDF will be saved in your system's temp directory with a timestamped filename.

You can customize the output folder and/or filename:

```php
$merger = new PdfMerger();
$merger->addFiles(['file1.pdf', 'file2.pdf']);
$merger->setOutputFolder('/path/to/output/folder');
$merger->setOutputFilename('merged-result.pdf');
$output = $merger->merge();
```

### Setting output file directly

Instead of setting output folder and filename separately, you can set the full output file path directly:

```php
$merger = new PdfMerger();
$merger->addFiles(['file1.pdf', 'file2.pdf']);
$merger->setOutputFile('/path/to/output/merged.pdf');
$output = $merger->merge();
```

### Setting Ghostscript executable path

If `gs` is not in your system PATH or you want to use a custom Ghostscript binary:

```php
$merger = new PdfMerger();
$merger->setGsPath('/usr/local/bin/gs');
```

### Setting compression level

Control the output PDF compression level with one of these presets:

- `/screen` (lowest quality, smallest size)
- `/ebook`
- `/printer`
- `/prepress`
- `/default` (default setting)

```php
$merger->setCompressionLevel('/ebook');
```

### Setting process timeout

By default, the Ghostscript process timeout is 60 seconds.
You can change it (in seconds) or disable by passing `null`:

```php
$merger->setTimeout(120);  // 2 minutes timeout
```

### Resetting the merger instance

You can reset the internal state (input files, output settings, compression, etc.) to start fresh:

```php
$merger = new PdfMerger();
$merger->addFiles(['file1.pdf', 'file2.pdf']);
$merger->setOutputFile('/path/to/output/merged.pdf');
$output = $merger->merge();

// Reset the instance to merge other files
$merger->reset();

$merger->addFiles(['file3.pdf', 'file4.pdf']);
$output2 = $merger->merge();
```

## Notes

- At least two PDF files are required for merging.
- The library throws exceptions on errors — use try-catch blocks for error handling in production.

## License

This project is open-sourced under the MIT license.

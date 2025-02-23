<?php

declare(strict_types=1);

namespace FE\SiteDynamic\FileSystem;

use SplFileInfo;

trait FileTrait
{
    private string $mimetype = '';

    final private function __construct(
        private SplFileInfo $fileInfo,
        private string $root
    ) {
    }

    public function notfound(): bool
    {
        return ! $this->found();
    }

    public function found(): bool
    {
        return $this->fileInfo()->isFile();
    }

    public function root(): string
    {
        return $this->root;
    }

    public function path(bool $full = true, bool $omitFilename = false): string
    {
        $realPath = $this->fileInfo()->getRealPath();

        if (! $realPath) {
            return '';
        }

        if ($omitFilename) {
            $parts = explode('/', $realPath);
            $parts = array_slice($parts, 0, -1);
            $realPath = implode('/', $parts);
        }

        if ($full) {
            return $realPath;
        }
        return str_replace($this->root(), '', $realPath);
    }

    public function mimetype(): string
    {
        if (strlen($this->mimetype) === 0) {
            $mimetype = match ($this->extension()) {
                'md'    => 'text/html',
                'css'   => 'text/css',
                'js'    => 'text/javascript',
                'xml'   => 'application/xml',
                default => mime_content_type($this->path())
            };

            if (! $mimetype) {
                return 'text/plain';

            } else {
                $this->mimetype = $mimetype;

            }
        }
        return $this->mimetype;
    }

    public function canonicalUrl(string $appUrl): string
    {
        return $appUrl . $this->path(full: false, omitFilename: true);
    }

    private function extension(): string
    {
        return $this->fileInfo()->getExtension();
    }

    private function fileInfo(): SplFileInfo
    {
        return $this->fileInfo;
    }
}

<?php

namespace Gesnovasalud\EnvironmentIndicator\Support;

class BranchResolver
{
    /**
     * Reads the current branch from .git/HEAD without spawning a shell.
     */
    public function resolve(?string $basePath = null): ?string
    {
        $headFile = ($basePath ?? base_path()) . '/.git/HEAD';

        if (! is_file($headFile) || ! is_readable($headFile)) {
            return null;
        }

        $head = trim((string) file_get_contents($headFile));

        if (! str_starts_with($head, 'ref: refs/heads/')) {
            return null;
        }

        return substr($head, strlen('ref: refs/heads/')) ?: null;
    }
}

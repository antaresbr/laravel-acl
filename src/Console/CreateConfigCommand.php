<?php

namespace Antares\Acl\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateConfigCommand extends Command
{
    protected $signature = 'antares:acl-create-config';

    protected $description = 'Create ACL package configuration file.';

    protected function relativeToBasePath($path)
    {
        if (Str::startsWith($path, base_path())) {
            return Str::replaceFirst(DIRECTORY_SEPARATOR, '', Str::after($path, base_path()));
        }
        return $path;
    }

    public function handle()
    {
        $targetFile = ai_acl_path('config/acl.php');
        $sourceFile = "{$targetFile}.template";

        if (is_file($targetFile)) {
            $this->warn('File already exists: ' . $this->relativeToBasePath($targetFile));
        } else {
            if (!is_file($sourceFile)) {
                $this->error('Template file not found: ' . $this->relativeToBasePath($sourceFile));
            } else {
                $sourceContent = file_get_contents($sourceFile);
                file_put_contents($targetFile, str_replace('{{jwt_key}}', Str::random(32), $sourceContent));
                $this->info('Created config file: ' . $this->relativeToBasePath($targetFile));
            }
        }
    }
}

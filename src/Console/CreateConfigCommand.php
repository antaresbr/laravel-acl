<?php

namespace Antares\Acl\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateConfigCommand extends Command
{
    protected $signature = 'acl:create-config';

    protected $description = 'Create ACL package configuration file.';

    public function handle()
    {
        $targetFile = acl_path('config/acl.php');
        $sourceFile = "{$targetFile}.template";

        if (is_file($targetFile)) {
            $this->warn('File already exists: '. $targetFile);
        }
        else {
            if (!is_file($sourceFile)) {
                $this->error('Template file not found: '. $sourceFile);
            }
            else {
                $sourceContent = file_get_contents($sourceFile);
                file_put_contents($targetFile, str_replace('{{jwt_key}}', Str::random(32), $sourceContent));
                $this->info('Created config file: '. $targetFile);
            }
        }
    }
}

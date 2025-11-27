<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CleanOrphanMedia extends Command
{
    public function configure(): void
    {
        $this->setName('media:clean-orphans')
            ->setDescription('Clean orphaned media files from the media library');
    }

    public function handle(): int
    {
        if (! $this->confirm('Are you sure you want to delete orphaned media files?')) {
            $this->components->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $orphanCount = 0;
        $bar = $this->output->createProgressBar(Media::count());

        Media::chunk(100, function ($mediaItems) use (&$orphanCount, $bar) {
            foreach ($mediaItems as $media) {
                if (! $this->modelExists($media)) {
                    try {
                        $media->delete();
                        $orphanCount++;

                        $this->components->info("Deleted orphaned media: {$media->file_name}");
                    } catch (\Exception $e) {
                        $this->components->error("Error deleting media {$media->id}: {$e->getMessage()}");
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->components->info("Cleaned {$orphanCount} orphaned media files");

        return self::SUCCESS;
    }

    private function modelExists(Media $media): bool
    {
        try {
            $modelClass = $media->model_type;
            if (class_exists($modelClass)) {
                $model = new $modelClass;

                return DB::table($model->getTable())
                    ->where('id', $media->model_id)
                    ->exists();
            }

            return false;
        } catch (\Exception $e) {
            $this->components->error("Error checking model {$media->model_type}: {$e->getMessage()}");

            return false;
        }
    }
}

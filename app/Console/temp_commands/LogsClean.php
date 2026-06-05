<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogsClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean 
                            {--days=30 : Remove logs older than X days}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--keep-laravel : Keep laravel.log file even if old}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clean old log files from storage/logs directory\n\n" .
                            "Usage:\n" .
                            "  php artisan logs:clean\n" .
                            "  php artisan logs:clean --days=60\n" .
                            "  php artisan logs:clean --dry-run\n" .
                            "  php artisan logs:clean --keep-laravel\n\n" .
                            "Options:\n" .
                            "  --days=NUMBER     Delete files older than NUMBER days (default: 30)\n" .
                            "  --dry-run         Show what would be deleted without actually deleting\n" .
                            "  --keep-laravel    Keep the main laravel.log file even if old\n\n" .
                            "Examples:\n" .
                            "  php artisan logs:clean                    # Delete files older than 30 days\n" .
                            "  php artisan logs:clean --days=90          # Delete files older than 90 days\n" .
                            "  php artisan logs:clean --dry-run          # Show what would be deleted\n" .
                            "  php artisan logs:clean --keep-laravel     # Keep laravel.log file";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $keepLaravelLog = $this->option('keep-laravel');
        
        $logPath = storage_path('logs');
        $cutoffTime = Carbon::now()->subDays($days)->getTimestamp();
        
        $deletedCount = 0;
        $totalSize = 0;
        $failedCount = 0;
        
        $this->info("🔍 Scanning log directory: {$logPath}");
        $this->info("🗑️  Will delete files older than {$days} days");
        
        if ($dryRun) {
            $this->warn("🧪 DRY RUN MODE: No files will be deleted");
        }
        
        // Get all files in logs directory
        try {
            $files = File::files($logPath);
            $totalFiles = count($files);
            
            $this->info("📊 Found {$totalFiles} files in logs directory");
            
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $filePath = $file->getPathname();
                $fileSize = $file->getSize();
                $lastModified = $file->getMTime();
                
                // Skip if we should keep laravel.log
                if ($keepLaravelLog && $fileName === 'laravel.log') {
                    continue;
                }
                
                // Check if file is old enough to delete
                if ($lastModified < $cutoffTime) {
                    $fileAge = floor((time() - $lastModified) / 86400);
                    $formattedSize = $this->formatBytes($fileSize);
                    
                    if ($dryRun) {
                        $this->line("🗑️  [DRY RUN] Would delete: {$fileName} ({$formattedSize}, {$fileAge} days old)");
                        $deletedCount++;
                        $totalSize += $fileSize;
                    } else {
                        try {
                            File::delete($filePath);
                            $this->line("✅ Deleted: {$fileName} ({$formattedSize}, {$fileAge} days old)");
                            $deletedCount++;
                            $totalSize += $fileSize;
                            Log::info("LogsClean: Deleted {$fileName} ({$formattedSize}, {$fileAge} days old)");
                        } catch (\Exception $e) {
                            $this->error("❌ Failed to delete: {$fileName} - {$e->getMessage()}");
                            $failedCount++;
                            Log::error("LogsClean: Failed to delete {$fileName} - {$e->getMessage()}");
                        }
                    }
                }
            }
            
            $this->newLine();
            
            // Show summary
            $formattedTotalSize = $this->formatBytes($totalSize);
            
            if ($dryRun) {
                $this->info("📊 DRY RUN SUMMARY:");
                $this->info("   Files that would be deleted: {$deletedCount}");
                $this->info("   Total space to be freed: {$formattedTotalSize}");
            } else {
                $this->info("📊 CLEANUP SUMMARY:");
                $this->info("   Files deleted: {$deletedCount}");
                $this->info("   Space freed: {$formattedTotalSize}");
                $this->info("   Failed deletions: {$failedCount}");
                
                if ($deletedCount > 0) {
                    Log::info("LogsClean: Deleted {$deletedCount} files, freed {$formattedTotalSize}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error scanning log directory: {$e->getMessage()}");
            Log::error("LogsClean: Error scanning directory - {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
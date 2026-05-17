<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\UserImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurgeTestOrders extends Command
{
    protected $signature = 'orders:purge-test {--force : Skip confirmation prompt}';

    protected $description = 'Delete ALL orders and their uploaded images from storage (for pre-production cleanup)';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $total = Order::withTrashed()->count();
            $this->warn("This will permanently delete {$total} orders and ALL related images.");
            if (! $this->confirm('Are you sure? This cannot be undone.')) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }

        $this->info('Collecting all order-related image IDs...');

        // Gather every UserImage ID referenced across all orders
        $userImageIds = collect();

        Order::withTrashed()->select([
            'front_image_id',
            'back_image_ids',
            'additional_image_id',
            'transparent_printing_id',
            'custom_design_image_id',
        ])->cursor()->each(function (Order $order) use (&$userImageIds) {
            if ($order->front_image_id) {
                $userImageIds->push($order->front_image_id);
            }

            if ($order->transparent_printing_id) {
                $userImageIds->push($order->transparent_printing_id);
            }

            foreach (['back_image_ids', 'additional_image_id', 'custom_design_image_id'] as $field) {
                $ids = $order->$field;
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                if (is_array($ids)) {
                    $userImageIds = $userImageIds->merge($ids);
                }
            }
        });

        $userImageIds = $userImageIds->unique()->filter()->values();
        $this->info("Found {$userImageIds->count()} user image records linked to orders.");

        // Delete physical user image files and records
        $deletedFiles  = 0;
        $deletedImages = 0;

        UserImage::whereIn('id', $userImageIds)->cursor()->each(function (UserImage $image) use (&$deletedFiles, &$deletedImages) {
            $path = $image->image_path;

            // image_path is stored as either just a filename or a full relative path
            if (! str_starts_with($path, 'user_images/')) {
                $path = 'user_images/' . $path;
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deletedFiles++;
            }

            $image->delete();
            $deletedImages++;
        });

        $this->info("Deleted {$deletedFiles} user image files, {$deletedImages} UserImage records.");

        // Delete designer upload files stored directly on the order
        $this->info('Deleting designer upload files...');
        $deletedDesignerFiles = 0;

        Order::withTrashed()->select([
            'designer_design_file',
            'designer_decoration_file',
            'designer_internal_files',
            'designer_gift_file',
        ])->cursor()->each(function (Order $order) use (&$deletedDesignerFiles) {
            $singleFields = ['designer_design_file', 'designer_decoration_file', 'designer_gift_file'];

            foreach ($singleFields as $field) {
                $path = $order->$field;
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deletedDesignerFiles++;
                }
            }

            $internalFiles = $order->designer_internal_files;
            if (is_string($internalFiles)) {
                $internalFiles = json_decode($internalFiles, true);
            }
            if (is_array($internalFiles)) {
                foreach ($internalFiles as $path) {
                    if ($path && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                        $deletedDesignerFiles++;
                    }
                }
            }
        });

        $this->info("Deleted {$deletedDesignerFiles} designer upload files.");

        // Delete all orders (notes cascade via DB foreign key)
        $this->info('Deleting all orders from database...');
        $orderCount = Order::withTrashed()->count();

        // Disable foreign key checks to handle any remaining constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Order::withTrashed()->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info("Deleted {$orderCount} orders (including soft-deleted).");

        // Delete any orphaned UserImage records not linked to an order
        $this->info('Cleaning up orphaned UserImage records...');
        $orphaned = UserImage::all();
        $orphanedCount = 0;
        $orphanedFiles = 0;

        foreach ($orphaned as $image) {
            $path = $image->image_path;
            if (! str_starts_with($path, 'user_images/')) {
                $path = 'user_images/' . $path;
            }
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $orphanedFiles++;
            }
            $image->delete();
            $orphanedCount++;
        }

        if ($orphanedCount > 0) {
            $this->info("Also removed {$orphanedCount} orphaned UserImage records and {$orphanedFiles} orphaned files.");
        }

        $this->info('');
        $this->info('Done. Summary:');
        $this->table(
            ['Item', 'Count'],
            [
                ['Orders deleted',           $orderCount],
                ['User image files deleted',  $deletedFiles + $orphanedFiles],
                ['UserImage records deleted', $deletedImages + $orphanedCount],
                ['Designer files deleted',    $deletedDesignerFiles],
            ]
        );

        return self::SUCCESS;
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Artisan;
use Log;

class Dashboard extends \Filament\Pages\Dashboard {
    public function getHeading(): string {
        return "Power - Dashboard";
    }

    public function getHeaderActions(): array {
        return [
            // \Filament\Actions\Action::make('Backup Database')
            //     ->button()
            //     ->requiresConfirmation()
            //     ->action(function () {
            //         try {
            //             // start the backup process
            //             Artisan::call('backup:run', ['--only-db' => 'true']);
            //             $output = Artisan::output();
            //             // log the results
            //             Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);

            //             // Get all files in the directory
            //             $backupFiles = File::files(storage_path('app/yoklah-absen'));

            //             // Sort files by modified time, descending
            //             usort($backupFiles, function ($a, $b) {
            //                 return -1 * strcmp($a->getMTime(), $b->getMTime());
            //             });

            //             // Check if there are any files
            //             if (count($backupFiles) > 0) {
            //                 // Get the latest file
            //                 $latestBackupFile = $backupFiles[0];

            //                 // Notification::make()
            //                 //     ->title('Backup successful')
            //                 //     ->body('Backup file created successfully')
            //                 //     ->success()
            //                 //     ->send();

            //                 // Return the download response
            //                 return response()->download($latestBackupFile)->deleteFileAfterSend(true);
            //             } else {
            //                 // If no files exist, return a notification
            //                 return Notification::make()
            //                     ->title('Backup failed')
            //                     ->danger()
            //                     ->send();
            //             }
            //         } catch (Exception $e) {
            //             // log the error
            //             Log::error("Backpack\BackupManager -- new backup failed from admin interface \r\n" . $e->getMessage());
            //             return Notification::make()
            //                 ->title('Backup failed')
            //                 ->body($e->getMessage())
            //                 ->danger()
            //                 ->send();
            //         }
            //     })
            //     ->icon('heroicon-o-circle-stack')
            //     ->color('info')
            //     ->label('Backup Database'),
        ];
    }
}

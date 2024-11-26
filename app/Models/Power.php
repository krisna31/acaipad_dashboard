<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Power extends Model
{
    /** @use HasFactory<\Database\Factories\PowerFactory> */
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    const BLE = 'BLE';
    const WIFI = 'WIFI';

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
            $model->created_by = auth()->user() ? auth()->user()->name : 'System';
        });

        static::updating(function ($model) {
            $model->updated_at = now();
            $model->updated_by = auth()->user()->name;
        });

        static::deleting(function ($model) {
            $model->deleted_at = now();
            $model->deleted_by = auth()->user()->name;
            $model->save();
            return \Filament\Notifications\Notification::make()
                ->success()
                ->title('Power has been deleted')
                ->body('The power was deleted successfully.');
        });
    }
}

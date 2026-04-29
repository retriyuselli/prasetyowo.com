<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'instagram',
        'kontrak',
        'phone',
        'address',
        'position',
        'salary',
        'date_of_birth',
        'date_of_join',
        'date_of_out',
        'no_rek',
        'user_id',
        'bank_name',
        'photo',
        'note',
    ];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataPribadi(): HasOne
    {
        return $this->hasOne(DataPribadi::class, 'email', 'email');
    }

    public function orderEvents(): BelongsToMany
    {
        return $this->belongsToMany(OrderEvent::class, 'order_event_employee')
            ->withPivot(['role', 'notes'])
            ->withTimestamps();
    }

    public function syncFromDataPribadi(bool $overwrite = false): bool
    {
        if (! $this->email) {
            return false;
        }

        $dataPribadi = DataPribadi::query()
            ->where('email', $this->email)
            ->first();

        if (! $dataPribadi) {
            return false;
        }

        $updates = [];

        if ($overwrite || ! $this->name) {
            if ($dataPribadi->nama_lengkap) {
                $updates['name'] = $dataPribadi->nama_lengkap;
            }
        }

        if ($overwrite || ! $this->phone) {
            if ($dataPribadi->nomor_telepon) {
                $updates['phone'] = $dataPribadi->nomor_telepon;
            }
        }

        if ($overwrite || ! $this->address) {
            if ($dataPribadi->alamat) {
                $updates['address'] = $dataPribadi->alamat;
            }
        }

        if ($overwrite || ! $this->bank_name) {
            if ($dataPribadi->bank_name) {
                $updates['bank_name'] = $dataPribadi->bank_name;
            }
        }

        if ($overwrite || ! $this->no_rek) {
            if ($dataPribadi->no_rekening) {
                $updates['no_rek'] = $dataPribadi->no_rekening;
            }
        }

        if ($overwrite || ! $this->photo) {
            $sourcePath = $dataPribadi->foto;
            if ($sourcePath) {
                $disk = Storage::disk('public');
                if ($disk->exists($sourcePath)) {
                    $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                    $fileName = (string) Str::uuid().($extension ? '.'.$extension : '');
                    $targetPath = 'employee-photos/'.$fileName;
                    if ($disk->copy($sourcePath, $targetPath)) {
                        $updates['photo'] = $targetPath;
                    }
                }
            }
        }

        if (empty($updates)) {
            return true;
        }

        $this->forceFill($updates)->saveQuietly();

        return true;
    }

    public function getEmCountAttribute()
    {
        $totEM = Order::where('employee_id', $this->id)->count();

        return $totEM;
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_join' => 'date',
            'date_of_out' => 'date',
            'salary' => 'integer',
        ];
    }
}

<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\DataPribadi;
use App\Models\Employee;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('syncAllFromDataPribadi')
                ->label('Sync Semua dari Data Pribadi')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Sync semua employee dari Data Pribadi')
                ->modalDescription('Membuat atau mengupdate Employee dari Data Pribadi (berdasarkan email).')
                ->modalSubmitActionLabel('Sync Semua')
                ->form([
                    Toggle::make('overwrite')
                        ->label('Timpa data Employee yang sudah ada')
                        ->default(false),
                ])
                ->visible(function (): bool {
                    if (! Auth::id()) {
                        return false;
                    }

                    return DB::table('model_has_roles')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('model_has_roles.model_type', '=', User::class)
                        ->where('model_has_roles.model_id', '=', Auth::id())
                        ->whereIn('roles.name', ['super_admin', 'admin'])
                        ->exists();
                })
                ->action(function (array $data): void {
                    $overwrite = (bool) ($data['overwrite'] ?? false);
                    $skipped = 0;
                    $created = 0;
                    $updated = 0;
                    $missingEmail = 0;

                    DataPribadi::query()
                        ->orderBy('id')
                        ->chunkById(200, function ($dataPribadis) use ($overwrite, &$created, &$updated, &$skipped, &$missingEmail) {
                            foreach ($dataPribadis as $dataPribadi) {
                                if (! $dataPribadi->email) {
                                    $missingEmail++;
                                    continue;
                                }

                                $employee = Employee::query()
                                    ->where('email', $dataPribadi->email)
                                    ->first();

                                $isNew = false;

                                if (! $employee) {
                                    $name = $dataPribadi->nama_lengkap ?: $dataPribadi->email;
                                    $baseSlug = Str::slug($name);
                                    $slug = $baseSlug;

                                    if (Employee::query()->where('slug', $slug)->exists()) {
                                        $slug = $baseSlug.'-'.$dataPribadi->id;
                                    }

                                    if (Employee::query()->where('slug', $slug)->exists()) {
                                        $slug = $baseSlug.'-'.Str::uuid();
                                    }

                                    $employee = new Employee();
                                    $employee->forceFill([
                                        'name' => $name,
                                        'slug' => $slug,
                                        'email' => $dataPribadi->email,
                                        'position' => 'Crew',
                                        'date_of_join' => $dataPribadi->tanggal_mulai_gabung ?? now()->toDateString(),
                                    ])->save();

                                    $isNew = true;
                                }

                                $before = $employee->updated_at;
                                $employee->syncFromDataPribadi($overwrite);
                                $employee->refresh();

                                if ($isNew) {
                                    $created++;
                                    continue;
                                }

                                if ($employee->updated_at && $before && $employee->updated_at->ne($before)) {
                                    $updated++;
                                } else {
                                    $skipped++;
                                }
                            }
                        });

                    Notification::make()
                        ->success()
                        ->title('Sync selesai')
                        ->body("Created: {$created} | Updated: {$updated} | Skipped: {$skipped} | Missing Email: {$missingEmail}")
                        ->send();
                }),
        ];
    }
}

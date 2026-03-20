<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\BuktiPekerjaanCs;
use App\Models\Cuti;
use App\Models\Jadwal;
use App\Models\JadwalKerjaCsBulanan;
use App\Models\LembarKerja;
use App\Models\LembarKerjaCs;
use App\Models\Pjlp;
use App\Models\User;
use App\Policies\BuktiPekerjaanCsPolicy;
use App\Policies\CutiPolicy;
use App\Policies\LembarKerjaCsPolicy;
use App\Policies\LembarKerjaPolicy;
use App\Policies\PjlpPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Bootstrap pagination
        Paginator::useBootstrapFive();

        // Register Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Pjlp::class, PjlpPolicy::class);
        Gate::policy(Cuti::class, CutiPolicy::class);
        Gate::policy(LembarKerja::class, LembarKerjaPolicy::class);
        Gate::policy(LembarKerjaCs::class, LembarKerjaCsPolicy::class);
        Gate::policy(BuktiPekerjaanCs::class, BuktiPekerjaanCsPolicy::class);
        Gate::policy(JadwalKerjaCsBulanan::class, BuktiPekerjaanCsPolicy::class);

        // Gate for Absensi import
        Gate::define('import', function ($user, $model) {
            return $user->can('absensi.import');
        });

        // Gate for Jadwal
        Gate::define('jadwal.create', fn($user) => $user->can('jadwal.manage'));
        Gate::define('jadwal.update', fn($user) => $user->can('jadwal.manage'));
        Gate::define('jadwal.delete', fn($user) => $user->can('jadwal.manage'));
    }
}

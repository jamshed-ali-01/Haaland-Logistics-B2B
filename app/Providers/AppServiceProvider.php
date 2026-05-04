<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load system settings into config
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $settings = \App\Models\SystemSetting::whereIn('key', [
                    'mail_host', 'mail_port', 'mail_encryption', 'mail_username', 
                    'mail_password', 'mail_from_address', 'mail_from_name'
                ])->get();

                $configMap = [
                    'mail_host' => 'mail.mailers.smtp.host',
                    'mail_port' => 'mail.mailers.smtp.port',
                    'mail_encryption' => 'mail.mailers.smtp.encryption',
                    'mail_username' => 'mail.mailers.smtp.username',
                    'mail_password' => 'mail.mailers.smtp.password',
                    'mail_from_address' => 'mail.from.address',
                    'mail_from_name' => 'mail.from.name',
                ];

                foreach ($settings as $setting) {
                    if ($setting->value) {
                        config([$configMap[$setting->key] => $setting->value]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail during initial setup/migrations
        }
    }
}

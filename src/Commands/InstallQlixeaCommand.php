<?php

namespace Qlixea\PaymentHub\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class InstallQlixeaCommand extends Command
{
    protected $signature = 'qlixea:install';
    protected $description = 'Instala y configura automáticamente Qlixea Payment Hub';

    public function handle()
    {
        $this->info('🚀 Iniciando instalación de Qlixea Payment Hub...');

        // 1. Publicar archivos
        $this->call('vendor:publish', ['--tag' => 'qlixea-config']);
        $this->call('vendor:publish', ['--tag' => 'qlixea-views']);

        // 2. Pedir datos al desarrollador
        $projectName = $this->ask('Nombre del Proyecto (ej: Mi Tienda Online)');
        $websiteUrl = $this->ask('URL base de tu sitio (ej: https://mitienda.com)');

        $this->info('⏳ Conectando con Qlixea Hub para aprovisionar tu proyecto...');

        // 3. Llamar a la API de Provisioning
        // ✅ CORREGIDO: Enviamos el header 'x-provisioning-token' para que coincida con tu middleware
        $response = Http::withHeaders([
            'x-provisioning-token' => config('qlixea.provisioning_master_token'),
            'Accept' => 'application/json',
        ])->post(config('qlixea.provisioning_api_url'), [
            'name' => $projectName,
            'website_url' => rtrim($websiteUrl, '/'),
        ]);

        if ($response->successful()) {
            $data = $response->json('data');
            
            // 4. Actualizar el archivo .env del cliente
            $this->updateEnvFile('QLIXEA_PROJECT_NAME', $projectName);
            $this->updateEnvFile('QLIXEA_API_KEY', $data['api_key']);
            $this->updateEnvFile('QLIXEA_API_SECRET', $data['api_secret']);
            $this->updateEnvFile('QLIXEA_DASHBOARD_URL', 'https://api.qlixea.com/dashboard');

            $this->info('✅ ¡Proyecto creado exitosamente en Qlixea Hub!');
            $this->info('🔑 API Key: ' . $data['api_key']);
            $this->info('🔒 API Secret: ' . $data['api_secret']);
            $this->info('🔗 Webhook configurado en: ' . $data['webhook_url']);
            
            $this->info('🎉 Instalación completada. No olvides ejecutar: php artisan config:cache');
        } else {
            $this->error('❌ Error al conectar con Qlixea Hub:');
            $this->error($response->body());
        }

        return Command::SUCCESS;
    }

    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $content = File::get($path);
            $value = str_replace('"', '', $value); // Limpiar comillas
            
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                $content .= "\n{$key}=\"{$value}\"";
            }
            File::put($path, $content);
        }
    }
}

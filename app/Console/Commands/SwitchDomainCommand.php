<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Komenda do przełączania między domeną lokalną a ngrok.
 *
 * Umożliwia łatwe przełączanie konfiguracji aplikacji między
 * różnymi środowiskami rozwojowymi.
 */
class SwitchDomainCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'domain:switch
                            {type : Typ domeny (local|ngrok)}
                            {--ngrok-url= : Nowy URL ngrok (opcjonalnie)}
                            {--rebuild-assets : Przebuduj assety po zmianie}';

    /**
     * Opis komendy konsoli.
     *
     * @var string
     */
    protected $description = 'Przełącza między domeną lokalną a ngrok dla rozwoju aplikacji';

    /**
     * Wykonuje komendę konsoli.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $ngrokUrl = $this->option('ngrok-url');
        $rebuildAssets = $this->option('rebuild-assets');

        if (! in_array($type, ['local', 'ngrok'])) {
            $this->error('❌ Nieprawidłowy typ domeny. Użyj: local lub ngrok');

            return Command::FAILURE;
        }

        $this->info("🔧 Przełączanie domeny na: {$type}");

        try {
            $envPath = base_path('.env');

            if (! File::exists($envPath)) {
                $this->error('❌ Plik .env nie został znaleziony!');

                return Command::FAILURE;
            }

            $envContent = File::get($envPath);

            if ($type === 'local') {
                // Przełącz na domenę lokalną
                $envContent = preg_replace('/^USE_NGROK=.*/m', 'USE_NGROK=false', $envContent);
                $this->info('✅ Przełączono na domenę lokalną: http://pethelp.test');
            } else {
                // Przełącz na ngrok
                if ($ngrokUrl) {
                    $envContent = preg_replace('/^NGROK_DOMAIN=.*/m', "NGROK_DOMAIN={$ngrokUrl}", $envContent);
                    $this->info("🔗 Zaktualizowano domenę ngrok na: {$ngrokUrl}");
                }

                $envContent = preg_replace('/^USE_NGROK=.*/m', 'USE_NGROK=true', $envContent);

                // Pobierz aktualną domenę ngrok
                preg_match('/^NGROK_DOMAIN=(.*)$/m', $envContent, $matches);
                $currentNgrok = $matches[1] ?? 'nieokreślona';
                $this->info("✅ Przełączono na domenę ngrok: {$currentNgrok}");
            }

            File::put($envPath, $envContent);

            // Wyczyść cache konfiguracji
            $this->info('🧹 Czyszczę cache konfiguracji...');
            Artisan::call('config:clear');

            // Przebuduj assety jeśli wymagane lub przełączamy na ngrok
            if ($rebuildAssets || $type === 'ngrok') {
                $this->info('🔄 Przebudowuję assety...');
                $this->call('build-assets');
            }

            $this->displayCurrentConfig();
            $this->info('🎉 Gotowe! Aplikacja została przełączona.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Błąd podczas przełączania domeny: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }

    /**
     * Wyświetla aktualną konfigurację domeny.
     */
    private function displayCurrentConfig(): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        preg_match('/^USE_NGROK=(.*)$/m', $envContent, $useNgrokMatch);
        preg_match('/^LOCAL_DOMAIN=(.*)$/m', $envContent, $localMatch);
        preg_match('/^NGROK_DOMAIN=(.*)$/m', $envContent, $ngrokMatch);

        $useNgrok = $useNgrokMatch[1] ?? 'false';
        $localDomain = $localMatch[1] ?? 'nieokreślona';
        $ngrokDomain = $ngrokMatch[1] ?? 'nieokreślona';

        $this->newLine();
        $this->info('📋 Aktualna konfiguracja:');
        $this->line("   USE_NGROK: {$useNgrok}");
        $this->line("   LOCAL_DOMAIN: {$localDomain}");
        $this->line("   NGROK_DOMAIN: {$ngrokDomain}");

        if ($useNgrok === 'true') {
            $this->info("   🌐 Aktywna domena: {$ngrokDomain}");
        } else {
            $this->info("   🏠 Aktywna domena: {$localDomain}");
        }
    }
}

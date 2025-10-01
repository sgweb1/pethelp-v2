<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Komenda do budowania assetów aplikacji.
 *
 * Uruchamia proces budowania assetów frontend'u (npm run build)
 * z odpowiednim obsługiwaniem błędów i raportowaniem.
 */
class BuildAssetsCommand extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'build-assets {--dev : Uruchom w trybie development}';

    /**
     * Opis komendy konsoli.
     *
     * @var string
     */
    protected $description = 'Buduje assety frontend aplikacji (CSS, JS)';

    /**
     * Wykonuje komendę konsoli.
     */
    public function handle(): int
    {
        $isDev = $this->option('dev');
        $command = $isDev ? 'npm run dev' : 'npm run build';

        $this->info('🔨 Budowanie assetów...');
        $this->info("Komenda: {$command}");

        try {
            $process = new Process(explode(' ', $command), base_path());
            $process->setTimeout(300); // 5 minut timeout

            $process->run(function ($type, $buffer) {
                $this->output->write($buffer);
            });

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->info('✅ Assety zostały pomyślnie zbudowane!');

            return Command::SUCCESS;

        } catch (ProcessFailedException $e) {
            $this->error('❌ Błąd podczas budowania assetów:');
            $this->error($e->getMessage());

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Nieoczekiwany błąd: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}

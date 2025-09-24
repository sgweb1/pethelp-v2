<?php

namespace App\Console\Commands;

use App\Services\TrelloService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TrelloSetup extends Command
{
    protected $signature = 'trello:setup
                           {--force : Force recreate board even if exists}
                           {--import-tasks : Import current project tasks}';

    protected $description = 'Setup Trello board structure for PetHelp project';

    public function handle(TrelloService $trello): int
    {
        $this->info('🎯 Setting up PetHelp Trello workspace...');
        $this->newLine();

        try {
            // Check if API credentials are configured
            if (!config('trello.api_key') || !config('trello.token')) {
                $this->error('❌ Trello API credentials not configured!');
                $this->warn('Please add TRELLO_API_KEY and TRELLO_TOKEN to your .env file');
                $this->info('Get your credentials from: https://trello.com/app-key');
                return self::FAILURE;
            }

            // Step 1: Create main board
            $this->info('📋 Creating main board...');
            $board = $trello->createPetHelpBoard();
            $boardId = $board['id'];
            $boardUrl = $board['url'];

            $this->info("✅ Board created successfully!");
            $this->info("   Board ID: {$boardId}");
            $this->info("   Board URL: {$boardUrl}");
            $this->newLine();

            // Step 2: Create lists
            $this->info('📝 Creating project lists...');
            $lists = $trello->createProjectLists($boardId);
            $this->info("✅ Created " . count($lists) . " lists");

            // Display created lists
            foreach ($lists as $list) {
                $this->info("   - {$list['name']} (ID: {$list['id']})");
            }
            $this->newLine();

            // Step 3: Create labels
            $this->info('🏷️ Creating project labels...');
            $labels = $trello->createProjectLabels($boardId);
            $this->info("✅ Created " . count($labels) . " labels");

            // Display created labels
            foreach ($labels as $label) {
                $this->info("   - {$label['name']} ({$label['color']})");
            }
            $this->newLine();

            // Step 4: Update .env file with board configuration
            $this->info('⚙️ Updating .env configuration...');
            $this->updateEnvFile($boardId, $boardUrl, $lists);
            $this->info("✅ Environment variables updated");
            $this->newLine();

            // Step 5: Import current tasks (optional)
            if ($this->option('import-tasks')) {
                $this->info('📥 Importing current project tasks...');
                $trello->importCurrentTasks($lists);
                $this->info("✅ Current tasks imported");
                $this->newLine();
            }

            // Step 6: Display next steps
            $this->displayNextSteps($boardUrl);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Setup failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function updateEnvFile(string $boardId, string $boardUrl, array $lists): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        // Create list ID mappings
        $listMappings = [];
        foreach ($lists as $list) {
            $name = strtolower($list['name']);
            if (str_contains($name, 'backlog')) {
                $listMappings['TRELLO_LIST_BACKLOG'] = $list['id'];
            } elseif (str_contains($name, 'sprint')) {
                $listMappings['TRELLO_LIST_SPRINT'] = $list['id'];
            } elseif (str_contains($name, 'development')) {
                $listMappings['TRELLO_LIST_DEVELOPMENT'] = $list['id'];
            } elseif (str_contains($name, 'testing')) {
                $listMappings['TRELLO_LIST_TESTING'] = $list['id'];
            } elseif (str_contains($name, 'review')) {
                $listMappings['TRELLO_LIST_REVIEW'] = $list['id'];
            } elseif (str_contains($name, 'done')) {
                $listMappings['TRELLO_LIST_DONE'] = $list['id'];
            } elseif (str_contains($name, 'deployed')) {
                $listMappings['TRELLO_LIST_DEPLOYED'] = $list['id'];
            }
        }

        // Add board configuration
        $newVars = [
            'TRELLO_BOARD_ID' => $boardId,
            'TRELLO_BOARD_URL' => $boardUrl,
            ...$listMappings
        ];

        foreach ($newVars as $key => $value) {
            if (str_contains($envContent, $key)) {
                // Update existing
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                // Add new
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }

    private function displayNextSteps(string $boardUrl): void
    {
        $this->info('🎉 Trello workspace setup complete!');
        $this->newLine();

        $this->info('📋 **Your Trello Board:**');
        $this->info("   {$boardUrl}");
        $this->newLine();

        $this->info('🚀 **Next Steps:**');
        $this->info('   1. Visit your Trello board and familiarize yourself with the structure');
        $this->info('   2. Add team members to the board');
        $this->info('   3. Enable Service Observer: php artisan make:observer ServiceObserver --model=Service');
        $this->info('   4. Setup webhooks for bi-directional sync');
        $this->info('   5. Add Trello widget to your dashboard');
        $this->newLine();

        $this->info('🔧 **Available Commands:**');
        $this->info('   php artisan trello:sync          - Sync with Trello manually');
        $this->info('   php artisan trello:import-tasks  - Import more tasks');
        $this->info('   php artisan trello:webhook       - Setup webhook endpoint');
        $this->newLine();

        $this->info('📚 **Documentation:**');
        $this->info('   Check C:\ProjeQtOr\pethelp\ for integration guides');
        $this->newLine();

        if ($this->confirm('Would you like to open the Trello board in your browser?', true)) {
            if (PHP_OS_FAMILY === 'Windows') {
                exec("start {$boardUrl}");
            } elseif (PHP_OS_FAMILY === 'Darwin') {
                exec("open {$boardUrl}");
            } else {
                exec("xdg-open {$boardUrl}");
            }
        }
    }
}
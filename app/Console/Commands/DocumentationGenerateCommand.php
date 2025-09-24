<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class DocumentationGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate {--missing : Generate only missing documentation} {--type=all : Type of docs to generate (api,components,models,services,javascript,blade,all)} {--force : Overwrite existing documentation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate documentation for API endpoints, Livewire components, models, and services';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🤖 Auto-generating documentation...');
        $this->newLine();

        $type = $this->option('type');
        $missingOnly = $this->option('missing');
        $force = $this->option('force');

        $generated = 0;

        if ($type === 'all' || $type === 'api') {
            $generated += $this->generateApiDocumentation($missingOnly, $force);
        }

        if ($type === 'all' || $type === 'components') {
            $generated += $this->generateComponentDocumentation($missingOnly, $force);
        }

        if ($type === 'all' || $type === 'models') {
            $generated += $this->generateModelDocumentation($missingOnly, $force);
        }

        if ($type === 'all' || $type === 'services') {
            $generated += $this->generateServiceDocumentation($missingOnly, $force);
        }

        if ($type === 'all' || $type === 'javascript') {
            $generated += $this->generateJavaScriptDocumentation($missingOnly, $force);
        }

        if ($type === 'all' || $type === 'blade') {
            $generated += $this->generateBladeDocumentation($missingOnly, $force);
        }

        $this->newLine();
        $this->info("✅ Generated $generated documentation files");
        $this->info('🔍 Run `php artisan docs:status` to check coverage');

        return Command::SUCCESS;
    }

    private function generateApiDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('📡 Generating API documentation...');

        $controllersPath = app_path('Http/Controllers/Api');
        $docsPath = base_path('docs/dev/reference/api');

        if (!File::exists($controllersPath)) {
            $this->warn('No API controllers found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $controllers = File::glob($controllersPath . '/*.php');

        foreach ($controllers as $controllerFile) {
            $className = pathinfo($controllerFile, PATHINFO_FILENAME);
            $docFile = $docsPath . '/' . $className . '.md';

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $className (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateApiControllerDoc($controllerFile, $className);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $className.md");
            $generated++;
        }

        return $generated;
    }

    private function generateComponentDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('🎨 Generating Livewire component documentation...');

        $componentsPath = app_path('Livewire');
        $docsPath = base_path('docs/dev/reference/components');

        if (!File::exists($componentsPath)) {
            $this->warn('No Livewire components found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $components = File::allFiles($componentsPath);

        foreach ($components as $componentFile) {
            $className = $componentFile->getFilenameWithoutExtension();
            $docFile = $docsPath . '/' . $className . '.md';

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $className (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateLivewireComponentDoc($componentFile->getPathname(), $className);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $className.md");
            $generated++;
        }

        return $generated;
    }

    private function generateModelDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('🗄️ Generating Model documentation...');

        $modelsPath = app_path('Models');
        $docsPath = base_path('docs/dev/reference/models');

        if (!File::exists($modelsPath)) {
            $this->warn('No Models found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $models = File::glob($modelsPath . '/*.php');

        foreach ($models as $modelFile) {
            $className = pathinfo($modelFile, PATHINFO_FILENAME);
            $docFile = $docsPath . '/' . $className . '.md';

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $className (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateModelDoc($modelFile, $className);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $className.md");
            $generated++;
        }

        return $generated;
    }

    private function generateServiceDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('⚙️ Generating Service documentation...');

        $servicesPath = app_path('Services');
        $docsPath = base_path('docs/dev/reference/services');

        if (!File::exists($servicesPath)) {
            $this->warn('No Services found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $services = File::glob($servicesPath . '/*.php');

        foreach ($services as $serviceFile) {
            $className = pathinfo($serviceFile, PATHINFO_FILENAME);
            $docFile = $docsPath . '/' . $className . '.md';

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $className (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateServiceDoc($serviceFile, $className);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $className.md");
            $generated++;
        }

        return $generated;
    }

    private function generateJavaScriptDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('📜 Generating JavaScript documentation...');

        $jsPath = resource_path('js');
        $docsPath = base_path('docs/dev/reference/javascript');

        if (!File::exists($jsPath)) {
            $this->warn('No JavaScript files found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $jsFiles = File::allFiles($jsPath);

        foreach ($jsFiles as $jsFile) {
            if ($jsFile->getExtension() !== 'js') {
                continue;
            }

            $fileName = $jsFile->getFilenameWithoutExtension();
            $docFile = $docsPath . '/' . $fileName . '.md';

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $fileName (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateJavaScriptDoc($jsFile->getPathname(), $fileName);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $fileName.md");
            $generated++;
        }

        return $generated;
    }

    private function generateBladeDocumentation(bool $missingOnly, bool $force): int
    {
        $this->info('🔧 Generating Blade components documentation...');

        $bladePath = resource_path('views/components');
        $docsPath = base_path('docs/dev/reference/blade-components');

        if (!File::exists($bladePath)) {
            $this->warn('No Blade components found');
            return 0;
        }

        File::ensureDirectoryExists($docsPath);
        $generated = 0;

        $bladeFiles = File::allFiles($bladePath);

        foreach ($bladeFiles as $bladeFile) {
            if ($bladeFile->getExtension() !== 'php') {
                continue;
            }

            $fileName = str_replace('/', '-', $bladeFile->getRelativePathname());
            $fileName = str_replace('.blade.php', '', $fileName);
            $docFile = $docsPath . '/' . $fileName . '.md';

            // Ensure directory exists for nested components
            File::ensureDirectoryExists(dirname($docFile));

            if ($missingOnly && File::exists($docFile)) {
                continue;
            }

            if (!$force && File::exists($docFile)) {
                $this->warn("  Skipping $fileName (already exists, use --force to overwrite)");
                continue;
            }

            $content = $this->generateBladeDoc($bladeFile->getPathname(), $fileName);
            File::put($docFile, $content);

            $this->line("  ✅ Generated: $fileName.md");
            $generated++;
        }

        return $generated;
    }

    private function generateApiControllerDoc(string $filePath, string $className): string
    {
        $content = File::get($filePath);
        $phpDocData = $this->parsePHPDoc($content, $className);

        return "# API Controller: $className

" . (($phpDocData['class']['description'] ?? null) ?: 'Automatycznie wygenerowana dokumentacja dla kontrolera API.') . "

## Opis
" . (($phpDocData['class']['longDescription'] ?? null) ?: "Kontroler API obsługujący operacje związane z " . Str::kebab($className) . ".") . "

" . (isset($phpDocData['class']['package']) && $phpDocData['class']['package'] ? "**Package**: `{$phpDocData['class']['package']}`\n" : '') . "
" . (isset($phpDocData['class']['author']) && $phpDocData['class']['author'] ? "**Author**: {$phpDocData['class']['author']}\n" : '') . "
" . (isset($phpDocData['class']['since']) && $phpDocData['class']['since'] ? "**Since**: {$phpDocData['class']['since']}\n" : '') . "

## Endpoints

" . $this->generateEndpointsListFromPHPDoc($phpDocData['methods'], $className) . "

## Methods

" . $this->generateMethodsListFromPHPDoc($phpDocData['methods']) . "

## Przykłady użycia

### Curl Examples
```bash
# GET request example
curl -X GET \\
  'http://pethelp.test/api/" . Str::kebab($className) . "' \\
  -H 'Accept: application/json' \\
  -H 'Authorization: Bearer YOUR_TOKEN'
```

## Response Formats

### Success Response
```json
{
  \"data\": {},
  \"message\": \"Success\",
  \"status\": 200
}
```

### Error Response
```json
{
  \"message\": \"Error message\",
  \"errors\": {},
  \"status\": 422
}
```

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*🤖 Generated from PHPDoc comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    private function generateLivewireComponentDoc(string $filePath, string $className): string
    {
        $content = File::get($filePath);
        $phpDocData = $this->parsePHPDoc($content, $className);

        // Parse Livewire-specific documentation
        $eventsEmitted = $this->extractLivewireEvents($content, 'dispatch');
        $eventsListened = $this->extractLivewireEvents($content, 'listen');
        $usageExample = $this->extractUsageExample($phpDocData['class']);

        return "# Livewire Component: $className

" . (($phpDocData['class']['description'] ?? null) ?: 'Automatycznie wygenerowana dokumentacja dla komponentu Livewire.') . "

## Opis
" . (($phpDocData['class']['longDescription'] ?? null) ?: "Komponent Livewire obsługujący funkcjonalność " . Str::kebab($className) . ".") . "

## Lokalizacja
- **Klasa**: `app/Livewire/$className.php`
- **Widok**: `resources/views/livewire/" . Str::kebab($className) . ".blade.php`

" . (isset($phpDocData['class']['package']) && $phpDocData['class']['package'] ? "**Package**: `{$phpDocData['class']['package']}`\n" : '') . "

## Properties
" . $this->generatePropertiesListFromPHPDoc($phpDocData['properties']) . "

## Methods
" . $this->generateMethodsListFromPHPDoc($phpDocData['methods']) . "

## Usage Example
```blade
" . ($usageExample ?: "<livewire:$className
    wire:key=\"$className-{{ \$id }}\"
/>") . "
```

## Events

### Events Emitted
" . ($eventsEmitted ? implode("\n", array_map(fn($event) => "- **$event**", $eventsEmitted)) : "- Brak wykrytych wydarzeń emitowanych") . "

### Events Listened
" . ($eventsListened ? implode("\n", array_map(fn($event) => "- **$event**", $eventsListened)) : "- Brak wykrytych wydarzeń nasłuchiwanych") . "

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*🤖 Generated from PHPDoc comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    private function generateModelDoc(string $filePath, string $className): string
    {
        $content = File::get($filePath);

        return "# Model: $className

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący " . Str::lower($className) . " w systemie.

## Lokalizacja
- **Plik**: `app/Models/$className.php`
- **Tabela**: " . Str::snake(Str::plural($className)) . "

## Pola bazy danych
" . $this->generateTableFields($className) . "

## Relationships
" . $this->extractModelRelationships($content) . "

## Scopes
" . $this->extractModelScopes($content) . "

## Mutators & Accessors
" . $this->extractModelMutatorsAccessors($content) . "

## Usage Examples

### Create
```php
\$" . Str::camel($className) . " = $className::create([
    // fields
]);
```

### Find
```php
\$" . Str::camel($className) . " = $className::find(\$id);
```

### Update
```php
\$" . Str::camel($className) . "->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    private function generateServiceDoc(string $filePath, string $className): string
    {
        $content = File::get($filePath);
        $methods = $this->extractServiceMethods($content);

        return "# Service: $className

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z " . Str::kebab($className) . ".

## Lokalizacja
- **Plik**: `app/Services/$className.php`

## Methods
" . $this->generateMethodsList($methods) . "

## Usage Example
```php
use App\\Services\\$className;

\$service = app($className::class);
// lub przez DI
public function __construct(private $className \$service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    private function extractControllerMethods(string $content): array
    {
        preg_match_all('/public function (\w+)\([^)]*\)/', $content, $matches);
        return $matches[1] ?? [];
    }

    private function extractLivewireProperties(string $content): array
    {
        preg_match_all('/public \$(\w+)/', $content, $matches);
        return $matches[1] ?? [];
    }

    private function extractLivewireMethods(string $content): array
    {
        preg_match_all('/public function (\w+)\([^)]*\)/', $content, $matches);
        return array_filter($matches[1] ?? [], function($method) {
            return !in_array($method, ['mount', 'render', '__construct']);
        });
    }

    private function extractServiceMethods(string $content): array
    {
        preg_match_all('/public function (\w+)\([^)]*\)/', $content, $matches);
        return array_filter($matches[1] ?? [], function($method) {
            return $method !== '__construct';
        });
    }

    private function generateEndpointsList(array $methods, string $className): string
    {
        $endpoints = [];
        $baseUri = '/api/' . Str::kebab($className);

        foreach ($methods as $method) {
            $httpMethod = $this->guessHttpMethod($method);
            $uri = $this->generateUri($baseUri, $method);
            $endpoints[] = "- **$httpMethod** `$uri` - " . ucfirst($method);
        }

        return empty($endpoints) ? "Brak wykrytych endpoints." : implode("\n", $endpoints);
    }

    private function generateMethodsList(array $methods): string
    {
        if (empty($methods)) {
            return "Brak publicznych metod.";
        }

        $list = [];
        foreach ($methods as $method) {
            $list[] = "### $method()
Opis metody $method.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
\$result = \$this->$method();
```";
        }

        return implode("\n\n", $list);
    }

    private function generatePropertiesList(array $properties): array
    {
        if (empty($properties)) {
            return ["Brak publicznych właściwości."];
        }

        $list = [];
        foreach ($properties as $property) {
            $list[] = "- **\$$property** - Opis właściwości $property";
        }

        return $list;
    }

    private function generateTableFields(string $className): string
    {
        return "| Pole | Typ | Opis |
|------|-----|------|
| id | bigint | Klucz główny |
| created_at | timestamp | Data utworzenia |
| updated_at | timestamp | Data aktualizacji |

*📝 Uzupełnij rzeczywiste pola tabeli*";
    }

    private function extractModelRelationships(string $content): string
    {
        preg_match_all('/public function (\w+)\([^)]*\)\s*{\s*return \$this->(hasMany|hasOne|belongsTo|belongsToMany)\([^)]+\)/', $content, $matches);

        if (empty($matches[1])) {
            return "Brak wykrytych relacji.";
        }

        $relationships = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $name = $matches[1][$i];
            $type = $matches[2][$i];
            $relationships[] = "- **$name()** - $type relationship";
        }

        return implode("\n", $relationships);
    }

    private function extractModelScopes(string $content): string
    {
        preg_match_all('/public function scope(\w+)\([^)]*\)/', $content, $matches);

        if (empty($matches[1])) {
            return "Brak wykrytych scope'ów.";
        }

        $scopes = [];
        foreach ($matches[1] as $scope) {
            $scopes[] = "- **$scope** - Query scope";
        }

        return implode("\n", $scopes);
    }

    private function extractModelMutatorsAccessors(string $content): string
    {
        preg_match_all('/public function (get|set)(\w+)Attribute\([^)]*\)/', $content, $matches);

        if (empty($matches[1])) {
            return "Brak wykrytych mutatorów/accessorów.";
        }

        $items = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $type = $matches[1][$i] === 'get' ? 'Accessor' : 'Mutator';
            $attribute = Str::snake($matches[2][$i]);
            $items[] = "- **$attribute** - $type";
        }

        return implode("\n", $items);
    }

    private function guessHttpMethod(string $method): string
    {
        if (Str::startsWith($method, 'store') || Str::startsWith($method, 'create')) return 'POST';
        if (Str::startsWith($method, 'update') || Str::startsWith($method, 'edit')) return 'PUT';
        if (Str::startsWith($method, 'destroy') || Str::startsWith($method, 'delete')) return 'DELETE';
        return 'GET';
    }

    private function generateUri(string $baseUri, string $method): string
    {
        if (in_array($method, ['show', 'update', 'destroy'])) {
            return $baseUri . '/{id}';
        }
        return $baseUri;
    }

    /**
     * Parsuje PHPDoc z pliku PHP i wyodrębnia informacje o klasie, właściwościach i metodach.
     *
     * @param string $content Zawartość pliku PHP
     * @param string $className Nazwa klasy
     * @return array Struktura z danymi PHPDoc
     */
    private function parsePHPDoc(string $content, string $className): array
    {
        $result = [
            'class' => [],
            'properties' => [],
            'methods' => []
        ];

        // Parse class PHPDoc
        $result['class'] = $this->parseClassPHPDoc($content);

        // Parse properties PHPDoc
        $result['properties'] = $this->parsePropertiesPHPDoc($content);

        // Parse methods PHPDoc
        $result['methods'] = $this->parseMethodsPHPDoc($content);

        return $result;
    }

    /**
     * Parsuje PHPDoc klasy.
     */
    private function parseClassPHPDoc(string $content): array
    {
        $classDoc = [];

        // Find class PHPDoc comment
        if (preg_match('/\/\*\*(.*?)\*\/\s*class\s+\w+/s', $content, $matches)) {
            $docComment = $matches[1];

            // Extract short description
            if (preg_match('/^\s*\*\s*(.+?)$/m', $docComment, $desc)) {
                $classDoc['description'] = trim($desc[1]);
            }

            // Extract long description
            preg_match_all('/^\s*\*\s*(.+?)$/m', $docComment, $allLines);
            $descriptions = [];
            $inLongDesc = false;

            foreach ($allLines[1] as $line) {
                $line = trim($line);
                if (empty($line)) {
                    $inLongDesc = true;
                    continue;
                }
                if (str_starts_with($line, '@')) {
                    break;
                }
                if ($inLongDesc) {
                    $descriptions[] = $line;
                }
            }

            if (!empty($descriptions)) {
                $classDoc['longDescription'] = implode(' ', $descriptions);
            }

            // Extract tags
            $tags = ['package', 'author', 'since'];
            foreach ($tags as $tag) {
                if (preg_match('/@' . $tag . '\s+(.+?)$/m', $docComment, $tagMatch)) {
                    $classDoc[$tag] = trim($tagMatch[1]);
                }
            }
        }

        return $classDoc;
    }

    /**
     * Parsuje PHPDoc właściwości klasy.
     */
    private function parsePropertiesPHPDoc(string $content): array
    {
        $properties = [];

        // Find all properties with PHPDoc
        preg_match_all('/\/\*\*(.*?)\*\/\s*public\s+(?:\w+\s+)?\$(\w+)/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $docComment = $match[1];
            $propertyName = $match[2];

            $property = [];

            // Extract description
            if (preg_match('/^\s*\*\s*(.+?)(?:\.|$)/m', $docComment, $desc)) {
                $property['description'] = trim($desc[1]);
            }

            // Extract @var tag
            if (preg_match('/@var\s+(\S+)(?:\s+(.+?))?$/m', $docComment, $varMatch)) {
                $property['type'] = trim($varMatch[1]);
                if (isset($varMatch[2])) {
                    $property['description'] = trim($varMatch[2]);
                }
            }

            $properties[$propertyName] = $property;
        }

        return $properties;
    }

    /**
     * Parsuje PHPDoc metod klasy.
     */
    private function parseMethodsPHPDoc(string $content): array
    {
        $methods = [];

        // Find all methods with PHPDoc
        preg_match_all('/\/\*\*(.*?)\*\/\s*public\s+function\s+(\w+)\s*\([^)]*\)/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $docComment = $match[1];
            $methodName = $match[2];

            // Skip special methods
            if (in_array($methodName, ['__construct', 'mount', 'render'])) {
                continue;
            }

            $method = [];

            // Extract short description
            if (preg_match('/^\s*\*\s*(.+?)(?:\.|$)/m', $docComment, $desc)) {
                $method['description'] = trim($desc[1]);
            }

            // Extract long description
            preg_match_all('/^\s*\*\s*(.+?)$/m', $docComment, $allLines);
            $descriptions = [];
            $inLongDesc = false;

            foreach ($allLines[1] as $line) {
                $line = trim($line);
                if (empty($line)) {
                    $inLongDesc = true;
                    continue;
                }
                if (str_starts_with($line, '@')) {
                    break;
                }
                if ($inLongDesc) {
                    $descriptions[] = $line;
                }
            }

            if (!empty($descriptions)) {
                $method['longDescription'] = implode(' ', $descriptions);
            }

            // Extract parameters
            preg_match_all('/@param\s+(\S+)\s+\$(\w+)\s*(.*)$/m', $docComment, $params, PREG_SET_ORDER);
            $method['parameters'] = [];
            foreach ($params as $param) {
                $method['parameters'][] = [
                    'type' => $param[1],
                    'name' => $param[2],
                    'description' => trim($param[3])
                ];
            }

            // Extract return type
            if (preg_match('/@return\s+(\S+)\s*(.*)$/m', $docComment, $returnMatch)) {
                $method['return'] = [
                    'type' => $returnMatch[1],
                    'description' => trim($returnMatch[2])
                ];
            }

            // Extract throws
            preg_match_all('/@throws\s+(\S+)\s*(.*)$/m', $docComment, $throws, PREG_SET_ORDER);
            $method['throws'] = [];
            foreach ($throws as $throw) {
                $method['throws'][] = [
                    'exception' => $throw[1],
                    'description' => trim($throw[2])
                ];
            }

            // Extract example
            if (preg_match('/@example\s*(.*?)(?:@|\*\/)/s', $docComment, $exampleMatch)) {
                $method['example'] = trim(preg_replace('/^\s*\*\s*/m', '', $exampleMatch[1]));
            }

            $methods[$methodName] = $method;
        }

        return $methods;
    }

    /**
     * Generuje listę endpoints na podstawie danych PHPDoc.
     */
    private function generateEndpointsListFromPHPDoc(array $methods, string $className): string
    {
        if (empty($methods)) {
            return "Brak wykrytych endpoints.";
        }

        $endpoints = [];
        $baseUri = '/api/' . Str::kebab($className);

        foreach ($methods as $methodName => $method) {
            $httpMethod = $this->guessHttpMethod($methodName);
            $uri = $this->generateUri($baseUri, $methodName);
            $description = $method['description'] ?? ucfirst($methodName);

            $endpoints[] = "- **$httpMethod** `$uri` - $description";
        }

        return implode("\n", $endpoints);
    }

    /**
     * Generuje dokumentację metod na podstawie PHPDoc.
     */
    private function generateMethodsListFromPHPDoc(array $methods): string
    {
        if (empty($methods)) {
            return "Brak publicznych metod z dokumentacją.";
        }

        $list = [];
        foreach ($methods as $methodName => $method) {
            $methodDoc = "### $methodName()\n";

            if (!empty($method['description'])) {
                $methodDoc .= $method['description'] . "\n\n";
            }

            if (!empty($method['longDescription'])) {
                $methodDoc .= $method['longDescription'] . "\n\n";
            }

            // Parameters
            if (!empty($method['parameters'])) {
                $methodDoc .= "**Parameters:**\n";
                foreach ($method['parameters'] as $param) {
                    $methodDoc .= "- `{$param['type']} \${$param['name']}` - {$param['description']}\n";
                }
                $methodDoc .= "\n";
            }

            // Return type
            if (!empty($method['return'])) {
                $methodDoc .= "**Returns:**\n- `{$method['return']['type']}` {$method['return']['description']}\n\n";
            }

            // Exceptions
            if (!empty($method['throws'])) {
                $methodDoc .= "**Throws:**\n";
                foreach ($method['throws'] as $throw) {
                    $methodDoc .= "- `{$throw['exception']}` {$throw['description']}\n";
                }
                $methodDoc .= "\n";
            }

            // Example
            if (!empty($method['example'])) {
                $methodDoc .= "**Example:**\n```php\n{$method['example']}\n```\n";
            }

            $list[] = $methodDoc;
        }

        return implode("\n", $list);
    }

    /**
     * Generuje listę właściwości na podstawie PHPDoc.
     */
    private function generatePropertiesListFromPHPDoc(array $properties): string
    {
        if (empty($properties)) {
            return "Brak publicznych właściwości z dokumentacją.";
        }

        $list = [];
        foreach ($properties as $propertyName => $property) {
            $type = $property['type'] ?? 'mixed';
            $description = $property['description'] ?? "Właściwość $propertyName";
            $list[] = "- **`$type` \$$propertyName** - $description";
        }

        return implode("\n", $list);
    }

    /**
     * Wyodrębnia eventy Livewire z kodu.
     */
    private function extractLivewireEvents(string $content, string $type): array
    {
        $events = [];

        if ($type === 'dispatch') {
            // Find dispatch calls
            preg_match_all('/\$this->dispatch\([\'"]([^\'"]+)[\'"]/', $content, $matches);
            $events = array_merge($events, $matches[1]);
        }

        if ($type === 'listen') {
            // Find listeners array or protected $listeners
            if (preg_match('/protected\s+\$listeners\s*=\s*\[(.*?)\]/s', $content, $match)) {
                preg_match_all('/[\'"]([^\'"]+)[\'"]/', $match[1], $matches);
                $events = array_merge($events, $matches[1]);
            }
        }

        return array_unique($events);
    }

    /**
     * Wyodrębnia przykład użycia z PHPDoc klasy.
     */
    private function extractUsageExample(array $classDoc): ?string
    {
        // Look for USAGE EXAMPLE in class documentation
        if (!empty($classDoc['longDescription'])) {
            if (preg_match('/USAGE EXAMPLE:\s*(.*?)(?:\n\n|\*\/|$)/s', $classDoc['longDescription'], $match)) {
                return trim($match[1]);
            }
        }

        return null;
    }

    /**
     * Generuje dokumentację dla pliku JavaScript.
     */
    private function generateJavaScriptDoc(string $filePath, string $fileName): string
    {
        $content = File::get($filePath);
        $jsDocData = $this->parseJSDoc($content, $fileName);

        return "# JavaScript Module: $fileName

" . ($jsDocData['description'] ?: 'Automatycznie wygenerowana dokumentacja dla modułu JavaScript.') . "

## Opis
" . ($jsDocData['longDescription'] ?: "Moduł JavaScript obsługujący funkcjonalność związaną z $fileName.") . "

## Lokalizacja
- **Plik**: `resources/js/$fileName.js`

" . ($jsDocData['version'] ? "**Version**: {$jsDocData['version']}\n" : '') . "
" . ($jsDocData['author'] ? "**Author**: {$jsDocData['author']}\n" : '') . "

## Classes
" . $this->generateJSClassesList($jsDocData['classes']) . "

## Functions
" . $this->generateJSFunctionsList($jsDocData['functions']) . "

## Constants
" . $this->generateJSConstantsList($jsDocData['constants']) . "

## Usage Examples

### Import
```javascript
// ES6 import
import { functionName } from './resources/js/$fileName.js';

// AMD require
const module = require('./resources/js/$fileName.js');
```

### Basic Usage
" . ($jsDocData['example'] ?: "```javascript\n// Dodaj przykład użycia\n```") . "

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*🤖 Generated from JSDoc comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    /**
     * Generuje dokumentację dla komponentu Blade.
     */
    private function generateBladeDoc(string $filePath, string $fileName): string
    {
        $content = File::get($filePath);
        $bladeData = $this->parseBladeComments($content, $fileName);

        $componentName = str_replace('-', '.', $fileName);

        return "# Blade Component: $fileName

" . ($bladeData['description'] ?: 'Automatycznie wygenerowana dokumentacja dla komponentu Blade.') . "

## Opis
" . ($bladeData['longDescription'] ?: "Komponent Blade wyświetlający interfejs użytkownika dla $fileName.") . "

## Lokalizacja
- **Plik**: `resources/views/components/$fileName.blade.php`
- **Użycie**: `<x-$componentName>`

## Parameters
" . $this->generateBladeParametersList($bladeData['parameters']) . "

## Slots
" . $this->generateBladeSlotsList($bladeData['slots']) . "

## CSS Classes
" . $this->generateBladeCSSList($bladeData['css']) . "

## Usage Example
```blade
" . ($bladeData['example'] ?: "<x-$componentName
    :param=\"\$value\"
    :param2=\"\$value2\"
>
    Slot content
</x-$componentName>") . "
```

## Dependencies
" . $this->generateBladeDependenciesList($bladeData['dependencies']) . "

---
*Auto-generated documentation - last updated: " . now()->format('Y-m-d H:i:s') . "*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*";
    }

    /**
     * Parsuje JSDoc z pliku JavaScript.
     */
    private function parseJSDoc(string $content, string $fileName): array
    {
        $result = [
            'description' => '',
            'longDescription' => '',
            'version' => '',
            'author' => '',
            'example' => '',
            'classes' => [],
            'functions' => [],
            'constants' => []
        ];

        // Parse file-level JSDoc
        if (preg_match('/\/\*\*(.*?)\*\//', $content, $matches)) {
            $docComment = $matches[1];

            // Extract description
            if (preg_match('/^\s*\*\s*(.+?)$/m', $docComment, $desc)) {
                $result['description'] = trim($desc[1]);
            }

            // Extract tags
            $tags = ['version', 'author'];
            foreach ($tags as $tag) {
                if (preg_match('/@' . $tag . '\s+(.+?)$/m', $docComment, $tagMatch)) {
                    $result[$tag] = trim($tagMatch[1]);
                }
            }
        }

        // Parse functions with JSDoc
        preg_match_all('/\/\*\*(.*?)\*\/\s*(?:function\s+(\w+)|(?:const|let|var)\s+(\w+)\s*=\s*(?:function|\([^)]*\)\s*=>))/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $docComment = $match[1];
            $functionName = $match[2] ?? $match[3] ?? 'anonymous';

            $function = [];

            // Extract description
            if (preg_match('/^\s*\*\s*(.+?)$/m', $docComment, $desc)) {
                $function['description'] = trim($desc[1]);
            }

            // Extract parameters
            preg_match_all('/@param\s+\{([^}]+)\}\s+(\w+)\s*-?\s*(.*)$/m', $docComment, $params, PREG_SET_ORDER);
            $function['parameters'] = [];
            foreach ($params as $param) {
                $function['parameters'][] = [
                    'type' => $param[1],
                    'name' => $param[2],
                    'description' => trim($param[3])
                ];
            }

            // Extract return type
            if (preg_match('/@returns?\s+\{([^}]+)\}\s*(.*)$/m', $docComment, $returnMatch)) {
                $function['return'] = [
                    'type' => $returnMatch[1],
                    'description' => trim($returnMatch[2])
                ];
            }

            $result['functions'][$functionName] = $function;
        }

        // Parse classes
        preg_match_all('/\/\*\*(.*?)\*\/\s*class\s+(\w+)/s', $content, $classMatches, PREG_SET_ORDER);

        foreach ($classMatches as $match) {
            $docComment = $match[1];
            $className = $match[2];

            $class = [];

            if (preg_match('/^\s*\*\s*(.+?)$/m', $docComment, $desc)) {
                $class['description'] = trim($desc[1]);
            }

            $result['classes'][$className] = $class;
        }

        return $result;
    }

    /**
     * Parsuje komentarze Blade.
     */
    private function parseBladeComments(string $content, string $fileName): array
    {
        $result = [
            'description' => '',
            'longDescription' => '',
            'parameters' => [],
            'slots' => [],
            'css' => [],
            'dependencies' => [],
            'example' => ''
        ];

        // Parse main Blade comment
        if (preg_match('/\{\{--(.*?)--\}\}/s', $content, $matches)) {
            $comment = $matches[1];

            // Extract description (first line)
            $lines = array_filter(array_map('trim', explode("\n", $comment)));
            if (!empty($lines)) {
                $result['description'] = array_shift($lines);
            }

            // Extract long description (until @param)
            $longDesc = [];
            foreach ($lines as $line) {
                if (str_starts_with($line, '@')) {
                    break;
                }
                if (!empty($line)) {
                    $longDesc[] = $line;
                }
            }
            $result['longDescription'] = implode(' ', $longDesc);

            // Extract parameters
            preg_match_all('/@param\s+(\S+)\s+\$(\w+)\s*(.*)$/m', $comment, $params, PREG_SET_ORDER);
            foreach ($params as $param) {
                $result['parameters'][] = [
                    'type' => $param[1],
                    'name' => $param[2],
                    'description' => trim($param[3])
                ];
            }

            // Extract example
            if (preg_match('/@example\s*(.*?)(?:@|$)/s', $comment, $exampleMatch)) {
                $result['example'] = trim($exampleMatch[1]);
            }
        }

        // Extract slots
        preg_match_all('/\{\{\s*\$(\w+)\s*\}\}/', $content, $slotMatches);
        $result['slots'] = array_unique($slotMatches[1]);

        // Extract CSS classes (basic extraction)
        preg_match_all('/class=["\']([^"\']+)["\']/', $content, $cssMatches);
        $result['css'] = array_unique($cssMatches[1]);

        return $result;
    }

    /**
     * Generuje listę klas JavaScript.
     */
    private function generateJSClassesList(array $classes): string
    {
        if (empty($classes)) {
            return "Brak wykrytych klas.";
        }

        $list = [];
        foreach ($classes as $className => $class) {
            $list[] = "### $className\n" . ($class['description'] ?? "Klasa $className");
        }

        return implode("\n\n", $list);
    }

    /**
     * Generuje listę funkcji JavaScript.
     */
    private function generateJSFunctionsList(array $functions): string
    {
        if (empty($functions)) {
            return "Brak wykrytych funkcji z dokumentacją.";
        }

        $list = [];
        foreach ($functions as $functionName => $function) {
            $funcDoc = "### $functionName()\n";

            if (!empty($function['description'])) {
                $funcDoc .= $function['description'] . "\n\n";
            }

            if (!empty($function['parameters'])) {
                $funcDoc .= "**Parameters:**\n";
                foreach ($function['parameters'] as $param) {
                    $funcDoc .= "- `{$param['type']} {$param['name']}` - {$param['description']}\n";
                }
                $funcDoc .= "\n";
            }

            if (!empty($function['return'])) {
                $funcDoc .= "**Returns:**\n- `{$function['return']['type']}` {$function['return']['description']}\n\n";
            }

            $list[] = $funcDoc;
        }

        return implode("\n", $list);
    }

    /**
     * Generuje listę stałych JavaScript.
     */
    private function generateJSConstantsList(array $constants): string
    {
        if (empty($constants)) {
            return "Brak wykrytych stałych.";
        }

        return implode("\n", $constants);
    }

    /**
     * Generuje listę parametrów Blade.
     */
    private function generateBladeParametersList(array $parameters): string
    {
        if (empty($parameters)) {
            return "Brak zdefiniowanych parametrów.";
        }

        $list = [];
        foreach ($parameters as $param) {
            $list[] = "- **`{$param['type']}` \${$param['name']}** - {$param['description']}";
        }

        return implode("\n", $list);
    }

    /**
     * Generuje listę slotów Blade.
     */
    private function generateBladeSlotsList(array $slots): string
    {
        if (empty($slots)) {
            return "Brak wykrytych slotów.";
        }

        $list = [];
        foreach ($slots as $slot) {
            $list[] = "- **\$$slot** - Slot dla $slot";
        }

        return implode("\n", $list);
    }

    /**
     * Generuje listę klas CSS.
     */
    private function generateBladeCSSList(array $css): string
    {
        if (empty($css)) {
            return "Brak wykrytych klas CSS.";
        }

        return "```css\n" . implode("\n", array_map(fn($class) => ".$class", $css)) . "\n```";
    }

    /**
     * Generuje listę zależności Blade.
     */
    private function generateBladeDependenciesList(array $dependencies): string
    {
        if (empty($dependencies)) {
            return "Brak wykrytych zależności.";
        }

        return implode("\n", array_map(fn($dep) => "- $dep", $dependencies));
    }
}

#!/bin/bash
# docs-monitor.sh - Skrypt monitorujący zmiany wymagające aktualizacji dokumentacji

clear
echo "🔍 Documentation Monitor - PetHelp"
echo "=================================="
echo ""

# Kolory dla lepszej czytelności
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Ścieżki do monitorowania
WATCHED_PATHS=(
  "app/Http/Controllers/Api/"
  "app/Livewire/"
  "app/Models/"
  "routes/api.php"
  "routes/web.php"
  "database/migrations/"
  "app/Services/"
)

echo "📋 Sprawdzanie zmian od ostatniego commit..."
echo ""

CHANGES_FOUND=false
TOTAL_CHANGES=0

# Sprawdź zmiany w każdej ścieżce
for path in "${WATCHED_PATHS[@]}"; do
  if [ -e "$path" ]; then
    # Sprawdź czy są zmiany w danej ścieżce
    if git diff --quiet HEAD~1 HEAD -- "$path" 2>/dev/null; then
      echo -e "✅ ${GREEN}$path${NC} - brak zmian"
    else
      CHANGES_FOUND=true
      echo -e "⚠️  ${YELLOW}$path${NC} - wykryto zmiany"

      # Pokaż szczegółowe zmiany
      CHANGED_FILES=$(git diff --name-only HEAD~1 HEAD -- "$path" 2>/dev/null)
      if [ ! -z "$CHANGED_FILES" ]; then
        echo "$CHANGED_FILES" | while read file; do
          if [ ! -z "$file" ]; then
            echo "    📁 $file"
            TOTAL_CHANGES=$((TOTAL_CHANGES + 1))

            # Sprawdź czy istnieje dokumentacja dla tego pliku
            check_documentation_exists "$file"
          fi
        done
      fi
    fi
  else
    echo -e "⚠️  ${YELLOW}$path${NC} - ścieżka nie istnieje"
  fi
done

echo ""
echo "=================================="

if [ "$CHANGES_FOUND" = true ]; then
    echo -e "🔴 ${RED}WYMAGANA AKTUALIZACJA DOKUMENTACJI!${NC}"
    echo ""
    echo "📊 Następne kroki:"
    echo "1. php artisan docs:status          - sprawdź status dokumentacji"
    echo "2. php artisan docs:generate --missing  - wygeneruj brakującą dokumentację"
    echo "3. Uruchom Documentation Specialist Agent"
    echo ""

    # Generuj raport
    generate_documentation_report

else
    echo -e "✅ ${GREEN}Brak zmian wymagających aktualizacji dokumentacji${NC}"
fi

echo ""
echo "🤖 Powered by Documentation Specialist Agent"
echo "📅 $(date)"

# Funkcja sprawdzająca istnienie dokumentacji
check_documentation_exists() {
    local file_path="$1"

    # Konwersja ścieżki do dokumentacji
    local doc_path=""

    if [[ $file_path == app/Http/Controllers/Api/* ]]; then
        doc_path="docs/dev/reference/api/$(basename "$file_path" .php).md"
    elif [[ $file_path == app/Livewire/* ]]; then
        doc_path="docs/dev/reference/components/$(basename "$file_path" .php).md"
    elif [[ $file_path == app/Models/* ]]; then
        doc_path="docs/dev/reference/models/$(basename "$file_path" .php).md"
    elif [[ $file_path == app/Services/* ]]; then
        doc_path="docs/dev/reference/services/$(basename "$file_path" .php).md"
    fi

    if [ ! -z "$doc_path" ]; then
        if [ -f "$doc_path" ]; then
            echo -e "      📚 ${GREEN}Dokumentacja istnieje${NC} - wymaga aktualizacji"
        else
            echo -e "      ❌ ${RED}Brak dokumentacji${NC} - wymaga utworzenia"
        fi
    fi
}

# Funkcja generująca raport
generate_documentation_report() {
    local report_file="docs/DOCUMENTATION_STATUS.md"
    local temp_file="$report_file.tmp"

    echo "# 📊 Status Dokumentacji - $(date +%Y-%m-%d)" > "$temp_file"
    echo "" >> "$temp_file"
    echo "## ⚠️ Wymagane aktualizacje:" >> "$temp_file"

    # Sprawdź coverage API
    local api_controllers=$(find app/Http/Controllers/Api/ -name "*.php" 2>/dev/null | wc -l)
    local api_docs=$(find docs/dev/reference/api/ -name "*.md" 2>/dev/null | wc -l)
    local api_coverage=0
    if [ $api_controllers -gt 0 ]; then
        api_coverage=$(( (api_docs * 100) / api_controllers ))
    fi

    echo "- **API Endpoints:** $api_docs/$api_controllers (${api_coverage}%) udokumentowanych" >> "$temp_file"

    # Sprawdź coverage komponentów Livewire
    local livewire_components=$(find app/Livewire/ -name "*.php" 2>/dev/null | wc -l)
    local livewire_docs=$(find docs/dev/reference/components/ -name "*.md" 2>/dev/null | wc -l)
    local livewire_coverage=0
    if [ $livewire_components -gt 0 ]; then
        livewire_coverage=$(( (livewire_docs * 100) / livewire_components ))
    fi

    echo "- **Livewire Components:** $livewire_docs/$livewire_components (${livewire_coverage}%) udokumentowanych" >> "$temp_file"

    # Sprawdź coverage modeli
    local models=$(find app/Models/ -name "*.php" 2>/dev/null | wc -l)
    local model_docs=$(find docs/dev/reference/models/ -name "*.md" 2>/dev/null | wc -l)
    local model_coverage=0
    if [ $models -gt 0 ]; then
        model_coverage=$(( (model_docs * 100) / models ))
    fi

    echo "- **Models:** $model_docs/$models (${model_coverage}%) udokumentowanych" >> "$temp_file"

    echo "" >> "$temp_file"
    echo "## 📈 Statystyki pokrycia:" >> "$temp_file"
    echo "- **Ogólne pokrycie:** $(( (api_coverage + livewire_coverage + model_coverage) / 3 ))%" >> "$temp_file"
    echo "- **Ostatnia aktualizacja:** $(date)" >> "$temp_file"
    echo "" >> "$temp_file"
    echo "*Auto-generated by Documentation Specialist Agent*" >> "$temp_file"

    # Zastąp stary plik
    mv "$temp_file" "$report_file"
    echo "📄 Raport zapisany: $report_file"
}
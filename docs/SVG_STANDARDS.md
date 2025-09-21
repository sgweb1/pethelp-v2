# Standardy SVG w aplikacji PetHelp

## Problem z błędami SVG

### Częsty błąd
```
Error: <path> attribute d: Expected arc flag ('0' or '1'), "… 0 1 1-6 0 3 3 0z"
```

### Przyczyna
W ścieżkach SVG arc commands (`a` lub `A`) wymagają flag łuku rozdzielonych spacjami.

## ✅ Prawidłowe formaty ścieżek SVG

### Arc Command (łuk)
```xml
<!-- ❌ BŁĘDNE - brak spacji między flagami a współrzędnymi -->
<path d="M15 11a3 3 0 1 1-6 0 3 3 0z"/>

<!-- ✅ PRAWIDŁOWE - spacja między flagami a współrzędnymi -->
<path d="M15 11a3 3 0 1 1 -6 0 3 3 0z"/>
```

### Składnia Arc Command
```
a rx ry x-axis-rotation large-arc-flag sweep-flag dx dy
```

- `large-arc-flag`: `0` lub `1` (czy wybierać większy łuk)
- `sweep-flag`: `0` lub `1` (kierunek rysowania łuku)
- **WAŻNE**: Musi być spacja między flagami a kolejnymi parametrami!

## 🔧 Jak naprawiać błędne SVG

### 1. Znajdź błędne ścieżki
```bash
# Znajdź potencjalnie problematyczne wzorce
grep -r "1 1-[0-9]" resources/views/
grep -r "0 1-[0-9]" resources/views/
```

### 2. Napraw składnię
```bash
# Zamień wszystkie wystąpienia w pliku
sed -i 's/1 1-\([0-9]\)/1 1 -\1/g' file.blade.php
```

### 3. Zweryfikuj poprawność
- Otwórz stronę w przeglądarce
- Sprawdź konsolę deweloperską (F12)
- Upewnij się, że nie ma błędów SVG

## 📏 Standardy dla nowych SVG

### 1. Zawsze używaj spacji w ścieżkach
```xml
<!-- ✅ Dobra praktyka -->
<path d="M10 10 L20 20 A5 5 0 0 1 30 30 Z"/>
```

### 2. Formatowanie ścieżek
- Używaj wielkich liter dla bezwzględnych współrzędnych (M, L, A)
- Używaj małych liter dla względnych współrzędnych (m, l, a)
- Dodawaj spacje między wszystkimi parametrami

### 3. Kopiowanie z zewnętrznych źródeł
Gdy kopiujesz SVG z:
- Heroicons
- Feather Icons
- Material Icons
- Innych źródeł

**Zawsze sprawdź ścieżki pod kątem brakujących spacji!**

## 🛠️ Narzędzia do walidacji

### 1. Online validators
- [SVG Path Visualizer](https://svg-path-visualizer.netlify.app/)
- [SVG Editor](https://boxy-svg.com/)

### 2. Browser DevTools
- F12 → Console
- Szukaj błędów związanych z `path attribute d`

### 3. Automated check (dla CI/CD)
```bash
#!/bin/bash
# Sprawdź problematyczne wzorce w SVG
if grep -r "1 1-[0-9]\|0 1-[0-9]" resources/views/; then
    echo "❌ Znaleziono potencjalnie błędne ścieżki SVG!"
    exit 1
else
    echo "✅ Wszystkie ścieżki SVG wyglądają poprawnie"
fi
```

## 📋 Checklist dla code review

Przy każdej zmianie zawierającej SVG sprawdź:

- [ ] Czy wszystkie arc commands mają spacje między flagami a współrzędnymi?
- [ ] Czy SVG renderuje się poprawnie w przeglądarce?
- [ ] Czy konsola deweloperska nie pokazuje błędów SVG?
- [ ] Czy ścieżki są czytelne i sformatowane?

## 🚀 Jak wykonywać prawidłowo

### Przy każdej zmianie SVG:

1. **Skopiuj nowy SVG**
2. **Sprawdź ścieżki** - szukaj wzorców typu `1-`, `0-`
3. **Dodaj spacje** tam gdzie potrzeba
4. **Przetestuj** w przeglądarce
5. **Sprawdź konsolę** - czy nie ma błędów

### Przykład naprawy:
```xml
<!-- PRZED naprawą -->
<path d="M15 11a3 3 0 1 1-6 0 3 3 0z"/>

<!-- PO naprawie -->
<path d="M15 11a3 3 0 1 1 -6 0 3 3 0z"/>
```

---

**💡 Pamiętaj**: Jeden brakujący spacja może zepsuć całe SVG i generować błędy w konsoli przy każdym renderowaniu!
# Camping Platform Design Review Workflow

## Cel
Automatyczna analiza designu stron platformy kempingowej z wykorzystaniem Playwright MCP.

## Kryteria Oceny

### 🎨 Spójność Wizualna
- Zgodność z brandingiem (emerald/green theme)
- Jednolite wykorzystanie kolorów i typografii
- Spójne margin i padding między elementami
- Prawidłowe użycie komponentów (buttons, cards, forms)

### 📱 Responsywność
- Mobile-first design (320px+)
- Tablet compatibility (768px+) 
- Desktop optimization (1024px+)
- Touch-friendly elements (min 44px)

### ♿ Accessibility
- Prawidłowy kontrast kolorów
- Alt text dla obrazów
- Keyboard navigation
- Screen reader compatibility
- ARIA labels gdzie potrzebne

### 🚀 Performance UX
- Loading states i animations
- Error states i feedback
- Intuitive navigation
- Clear call-to-action buttons

### 🏕️ Specyfika Aplikacji Kempingowej
- Czytelna prezentacja miejsc kempingowych
- Intuitive filtering i search
- Map integration usability
- Review system clarity
- Photo gallery quality

## Workflow Steps

1. **Uruchom serwer**: `php artisan serve`
2. **Otwórz stronę**: Użyj Playwright MCP do nawigacji
3. **Wykonaj screenshoty**: Desktop, tablet, mobile
4. **Przeprowadź audyt**: Accessibility, performance
5. **Sprawdź interakcje**: Hover states, forms, buttons
6. **Generuj raport**: Problemy + rekomendacje

## Przykład Użycia

```
Użyj playwright mcp żeby przejść na localhost:8000, wykonaj design review zgodnie z workflow dla aplikacji kempingowej. Sprawdź stronę główną, listing miejsc kempingowych i detale miejsca.
```

## Dostosowanie dla Park4Night Style

Nasz design wzoruje się na park4night.com:
- Kartowy layout miejsc
- System gwiazdek (ratings)
- Clean iconografia
- Emerald/green color scheme
- Mobile-friendly interface

## Output Format

**Raport Design Review:**
- ✅ **Pozytywne aspekty**
- ⚠️ **Problemy do naprawy** 
- 💡 **Rekomendacje**
- 📸 **Screenshots z problemami**
- 🎯 **Action items**
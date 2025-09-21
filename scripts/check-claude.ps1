Write-Host "🔍 Sprawdzanie statusu Claude..." -ForegroundColor Cyan
Write-Host ""

Write-Host "📊 Status autoryzacji:" -ForegroundColor Yellow
try {
    claude auth status
} catch {
    Write-Host "❌ Błąd sprawdzania autoryzacji: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "📋 Wersja Claude:" -ForegroundColor Yellow
try {
    claude --version
} catch {
    Write-Host "❌ Błąd sprawdzania wersji: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "✅ Sprawdzanie zakończone" -ForegroundColor Green
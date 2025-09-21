#!/usr/bin/env node

import { execSync } from 'child_process';

console.log('🔍 Sprawdzanie planu Claude...\n');

try {
    // Sprawdź status autoryzacji
    console.log('📊 Status autoryzacji:');
    const authStatus = execSync('claude auth status', { encoding: 'utf8', stdio: 'pipe' });
    console.log(authStatus);

    // Sprawdź dostępne komendy
    console.log('\n📋 Dostępne komendy Claude:');
    const helpOutput = execSync('claude --help', { encoding: 'utf8', stdio: 'pipe' });
    console.log(helpOutput);

} catch (error) {
    if (error.status === 1) {
        console.log('❌ Claude nie jest zalogowany lub wystąpił błąd');
        console.log('Uruchom: claude auth login');
    } else {
        console.log('❌ Błąd podczas sprawdzania Claude:', error.message);
    }
}

console.log('\n✅ Sprawdzanie zakończone');
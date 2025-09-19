class ErrorLogger {
    constructor() {
        this.logs = [];
        this.maxLogs = 100;
        this.init();
    }

    init() {
        // Przechwytuj błędy JavaScript
        window.addEventListener('error', (event) => {
            this.logError({
                type: 'javascript_error',
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error?.stack,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            });
        });

        // Przechwytuj Promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.logError({
                type: 'promise_rejection',
                message: event.reason?.message || event.reason,
                stack: event.reason?.stack,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            });
        });

        // Przechwytuj błędy konsoli
        this.interceptConsole();

        // Wysyłaj logi co 30 sekund lub gdy jest ich więcej niż 10
        setInterval(() => this.sendLogs(), 30000);

        // Wyślij logi przed zamknięciem strony
        window.addEventListener('beforeunload', () => {
            this.sendLogs(true);
        });
    }

    interceptConsole() {
        const originalError = console.error;
        const originalWarn = console.warn;
        const originalLog = console.log;

        console.error = (...args) => {
            this.logError({
                type: 'console_error',
                message: args.join(' '),
                timestamp: new Date().toISOString(),
                url: window.location.href
            });
            originalError.apply(console, args);
        };

        console.warn = (...args) => {
            this.logError({
                type: 'console_warn',
                message: args.join(' '),
                timestamp: new Date().toISOString(),
                url: window.location.href
            });
            originalWarn.apply(console, args);
        };

        // Opcjonalnie loguj też console.log dla debugowania
        if (window.logAllConsole) {
            console.log = (...args) => {
                this.logError({
                    type: 'console_log',
                    message: args.join(' '),
                    timestamp: new Date().toISOString(),
                    url: window.location.href
                });
                originalLog.apply(console, args);
            };
        }
    }

    logError(errorData) {
        this.logs.push(errorData);

        // Ograniczenie liczby logów w pamięci
        if (this.logs.length > this.maxLogs) {
            this.logs = this.logs.slice(-this.maxLogs);
        }

        // Wyślij natychmiast krytyczne błędy
        if (errorData.type === 'javascript_error' || errorData.type === 'promise_rejection') {
            this.sendLogs();
        }
    }

    async sendLogs(isBeforeUnload = false) {
        if (this.logs.length === 0) return;

        const logsToSend = [...this.logs];
        this.logs = [];

        const payload = {
            logs: logsToSend,
            session_id: this.getSessionId(),
            page_info: {
                url: window.location.href,
                title: document.title,
                viewport: {
                    width: window.innerWidth,
                    height: window.innerHeight
                }
            }
        };

        try {
            const method = isBeforeUnload ? 'sendBeacon' : 'fetch';

            if (method === 'sendBeacon' && navigator.sendBeacon) {
                navigator.sendBeacon('/api/js-logs', JSON.stringify(payload));
            } else {
                await fetch('/api/js-logs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(payload)
                });
            }
        } catch (error) {
            // Jeśli wysyłanie nie udało się, przywróć logi
            this.logs.unshift(...logsToSend);
            console.warn('Failed to send error logs:', error);
        }
    }

    getSessionId() {
        let sessionId = sessionStorage.getItem('error_logger_session');
        if (!sessionId) {
            sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('error_logger_session', sessionId);
        }
        return sessionId;
    }

    // Metoda do ręcznego logowania
    log(message, type = 'custom', data = {}) {
        this.logError({
            type: type,
            message: message,
            data: data,
            timestamp: new Date().toISOString(),
            url: window.location.href
        });
    }

    // Metoda do pobierania aktualnych logów (dla debugowania)
    getCurrentLogs() {
        return [...this.logs];
    }

    // Metoda do czyszczenia logów
    clearLogs() {
        this.logs = [];
    }
}

// Inicjalizuj logger globalnie
window.errorLogger = new ErrorLogger();

// Dodaj globalne metody dla łatwości użycia
window.logError = (message, data) => window.errorLogger.log(message, 'manual_error', data);
window.logInfo = (message, data) => window.errorLogger.log(message, 'manual_info', data);
window.getErrorLogs = () => window.errorLogger.getCurrentLogs();
window.clearErrorLogs = () => window.errorLogger.clearLogs();

export default ErrorLogger;
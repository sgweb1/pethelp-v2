const Tail = require("tail").Tail;
const fs = require('fs');
const path = require('path');

// Konfiguracja plików do monitorowania
const logFiles = {
  laravel: "C:/laragon/www/pethelp/storage/logs/laravel.log",
  jsErrors: `C:/laragon/www/pethelp/storage/app/logs/js-errors-${new Date().toISOString().split('T')[0]}.log`
};

const colors = {
  reset: '\x1b[0m',
  red: '\x1b[31m',
  green: '\x1b[32m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m',
  magenta: '\x1b[35m',
  cyan: '\x1b[36m',
  gray: '\x1b[90m'
};

const tails = {};

function formatTimestamp() {
  return new Date().toLocaleTimeString('pl-PL');
}

function formatLogLine(source, line) {
  const timestamp = `${colors.gray}[${formatTimestamp()}]${colors.reset}`;
  const sourceLabel = source === 'laravel'
    ? `${colors.blue}[LARAVEL]${colors.reset}`
    : `${colors.magenta}[JS-ERROR]${colors.reset}`;

  return `${timestamp} ${sourceLabel} ${line}`;
}

function formatJSErrorLine(line) {
  try {
    const logData = JSON.parse(line);
    const typeColor = logData.type === 'javascript_error' || logData.type === 'console_error'
      ? colors.red
      : logData.type === 'console_warn'
        ? colors.yellow
        : colors.cyan;

    const sessionShort = logData.session_id ? logData.session_id.substr(-8) : 'unknown';
    const time = new Date(logData.timestamp).toLocaleTimeString('pl-PL');

    return `${colors.gray}[${time}] ${typeColor}[${logData.type.toUpperCase()}]${colors.reset} ` +
           `${colors.gray}(${sessionShort})${colors.reset} ${logData.message}` +
           (logData.filename ? `\n         ${colors.gray}↳ ${logData.filename}:${logData.line || '?'}${colors.reset}` : '');
  } catch (e) {
    return line; // Fallback jeśli nie jest to JSON
  }
}

function startMonitoring(file, source) {
  if (!fs.existsSync(file)) {
    console.log(`${colors.yellow}⚠ File not found: ${file}${colors.reset}`);
    return null;
  }

  const tail = new Tail(file);

  tail.on("line", (line) => {
    if (source === 'jsErrors') {
      console.log(formatLogLine(source, formatJSErrorLine(line)));
    } else {
      console.log(formatLogLine(source, line));
    }
  });

  tail.on("error", (error) => {
    console.error(`${colors.red}ERROR monitoring ${source}: ${error}${colors.reset}`);
  });

  return tail;
}

// Główna funkcja
function main() {
  console.log(`${colors.green}🚀 Starting Enhanced Log Monitor${colors.reset}`);
  console.log(`${colors.gray}Press Ctrl+C to stop${colors.reset}`);
  console.log("---");

  // Monitoruj Laravel logs
  console.log(`${colors.blue}📊 Monitoring Laravel logs: ${logFiles.laravel}${colors.reset}`);
  tails.laravel = startMonitoring(logFiles.laravel, 'laravel');

  // Monitoruj JS error logs
  console.log(`${colors.magenta}🐞 Monitoring JS error logs: ${logFiles.jsErrors}${colors.reset}`);
  tails.jsErrors = startMonitoring(logFiles.jsErrors, 'jsErrors');

  // Sprawdzaj nowe pliki JS errors co minutę
  setInterval(() => {
    const currentDate = new Date().toISOString().split('T')[0];
    const newJsFile = `C:/laragon/www/pethelp/storage/app/logs/js-errors-${currentDate}.log`;

    if (newJsFile !== logFiles.jsErrors && fs.existsSync(newJsFile)) {
      console.log(`${colors.yellow}🔄 Switching to new JS log file: ${newJsFile}${colors.reset}`);

      if (tails.jsErrors) {
        tails.jsErrors.unwatch();
      }

      logFiles.jsErrors = newJsFile;
      tails.jsErrors = startMonitoring(logFiles.jsErrors, 'jsErrors');
    }
  }, 60000); // Co minutę

  // Pokaż statystyki co 5 minut
  setInterval(() => {
    console.log(`${colors.cyan}📈 Monitor działa od: ${formatTimestamp()}${colors.reset}`);
  }, 300000); // Co 5 minut
}

// Graceful shutdown
process.on('SIGINT', () => {
  console.log(`\n${colors.yellow}🛑 Stopping log monitor...${colors.reset}`);

  Object.values(tails).forEach(tail => {
    if (tail) {
      tail.unwatch();
    }
  });

  console.log(`${colors.green}✅ Log monitor stopped${colors.reset}`);
  process.exit(0);
});

// Obsługa błędów procesu
process.on('uncaughtException', (error) => {
  console.error(`${colors.red}💥 Uncaught Exception: ${error}${colors.reset}`);
  process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error(`${colors.red}💥 Unhandled Rejection at: ${promise}, reason: ${reason}${colors.reset}`);
});

// Start aplikacji
main();
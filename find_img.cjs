const fs = require('fs');
const html = fs.readFileSync('msu.html', 'utf8');
const urls = html.match(/https?:\/\/[^"'\s<>]*logo[^"'\s<>]*\.(?:png|webp|svg|jpg)/gi) || [];
console.log([...new Set(urls)].join('\n') || 'not found');

const fs = require('fs');
const html = fs.readFileSync('msu.html', 'utf8');
const match = html.match(/src="([^"]*(?:logo|msu)[^"]*\.(png|webp|svg))"/i);
console.log(match ? match[1] : 'not found');

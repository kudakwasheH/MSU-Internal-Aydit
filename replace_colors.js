const fs = require('fs');
const path = require('path');

const dir = 'C:/Users/Munashe/Desktop/internal/prot/resources';

function walkDir(currentPath) {
    const files = fs.readdirSync(currentPath);
    for (const file of files) {
        const fullPath = path.join(currentPath, file);
        if (fs.statSync(fullPath).isDirectory()) {
            walkDir(fullPath);
        } else if (fullPath.endsWith('.blade.php') || fullPath.endsWith('.css')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let newContent = content
                .replace(/#800000/g, '#00265B')
                .replace(/#FFD700/g, '#F5C735')
                .replace(/#660000/g, '#001533');
            if (content !== newContent) {
                fs.writeFileSync(fullPath, newContent, 'utf8');
                console.log('Updated:', fullPath);
            }
        }
    }
}

walkDir(dir);
console.log('Done.');

const fs = require('fs');
const content = fs.readFileSync('C:\\Users\\GMS\\Downloads\\pdf_dump.txt', 'utf-8');
const streams = content.split('\n---STREAM---\n');
console.log('Total streams:', streams.length);
let count = 0;
streams.forEach((s, i) => {
    if (s.toLowerCase().includes('manage') || s.toLowerCase().includes('user') || s.toLowerCase().includes('examination') || s.toLowerCase().includes('resource')) {
        count++;
        console.log('Stream', i, 'length:', s.length);
        console.log(s.slice(0, 300));
        console.log('-------------------------');
    }
});
console.log('Matching streams:', count);


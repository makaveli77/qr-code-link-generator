const fs = require('fs');
const path = require('path');
const file = path.join(process.cwd(), 'public/js/dashboard.js');
let content = fs.readFileSync(file, 'utf8');
const target = "showToast('QR Code Link Failed To Create', 'bg-red-500');";
const replacement = "const errorData = await res.json().catch(() => ({})); let errorMessage = 'QR Code Link Failed To Create'; if (errorData.errors) { errorMessage = Object.values(errorData.errors).flat().join(' '); } else if (errorData.message) { errorMessage = errorData.message; } showToast(errorMessage, 'bg-red-500');";
if (content.includes(target)) {
    fs.writeFileSync(file, content.replace(target, replacement));
    console.log('Fixed SUCCESS');
} else {
    console.log('Target string not found');
}

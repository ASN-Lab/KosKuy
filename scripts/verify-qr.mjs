/**
 * Prints the QR matrix as ASCII so the generated code can be eyeballed without
 * an image viewer, and asserts the matrix has the expected QR structure.
 */

import QRCode from 'qrcode';

const base = (process.env.KOSKUY_URL || '').replace(/\/+$/, '');
const target = `${base}${process.env.KOSKUY_QR_PATH || '/register'}`;

const qr = QRCode.create(target, { errorCorrectionLevel: 'H' });
const size = qr.modules.size;
const data = qr.modules.data;

console.log(`target: ${target}`);
console.log(`version: ${qr.version}  modules: ${size}x${size}\n`);

for (let y = 0; y < size; y++) {
  let row = '';
  for (let x = 0; x < size; x++) {
    row += data[y * size + x] ? '██' : '  ';
  }
  console.log(row);
}

// Structural sanity: the three finder patterns are 7x7 dark rings in the corners.
const isFinderAt = (ox, oy) => {
  for (let y = 0; y < 7; y++) {
    for (let x = 0; x < 7; x++) {
      const onRing = x === 0 || y === 0 || x === 6 || y === 6;
      const inCore = x >= 2 && x <= 4 && y >= 2 && y <= 4;
      const expected = onRing || inCore;
      if (!!data[(oy + y) * size + (ox + x)] !== expected) return false;
    }
  }
  return true;
};

const finders = [isFinderAt(0, 0), isFinderAt(size - 7, 0), isFinderAt(0, size - 7)];
console.log(`\nfinder patterns (TL, TR, BL): ${finders.join(', ')}`);

if (qr.version < 1 || !finders.every(Boolean)) {
  console.error('FAILED: matrix does not look like a valid QR code');
  process.exit(1);
}

console.log('OK: matrix has valid QR structure');

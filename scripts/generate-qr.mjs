/**
 * Generates the signup QR code used on the printable flyer.
 *
 * Reads the target URL from the environment so the committed image always
 * matches whatever KOSKUY_URL is configured:
 *
 *   npm.cmd run qr
 *
 * The generated PNG is written to public/images/newsletter-qr.png, which is
 * what KOSKUY_NEWSLETTER_QR should point at.
 */

import { writeFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import QRCode from 'qrcode';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const output = resolve(root, 'public/images/newsletter-qr.png');

const base = (process.env.KOSKUY_URL || '').replace(/\/+$/, '');
const path = process.env.KOSKUY_QR_PATH || '/register';
const target = `${base}${path}`;

if (!base) {
  console.error('KOSKUY_URL is not set, so the QR code would point nowhere.');
  console.error('Add KOSKUY_URL="https://your-domain.tld" to .env, then re-run `npm run qr`.');
  process.exit(1);
}

const png = await QRCode.toBuffer(target, {
  type: 'png',
  errorCorrectionLevel: 'H',
  margin: 1,
  width: 600,
  color: { dark: '#0f172a', light: '#ffffff' },
});

await mkdir(dirname(output), { recursive: true });
await writeFile(output, png);

console.log(`Wrote ${output}`);
console.log(`Encodes: ${target}`);

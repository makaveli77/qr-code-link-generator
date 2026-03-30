# Troubleshooting Guide

Find solutions to common issues you might encounter while creating, printing, and using your QR codes.

## Printed QR Code Looks Blurry
If your printed QR code is blurry or scanning incorrectly, the issue is typically the file format.
- **The Fix:** When downloading your QR code for professional printing (flyers, banners, posters), **always use vector formats like SVG or EPS.**
- **Why this matters:** Unlike PNG or JPG (which are raster and pixelate when stretched), SVG or EPS formats can be scaled to any size without losing quality. If you want a small code on a business card or a massive code on a billboard, an SVG will always remain perfectly crisp.

## QR Code is Not Scanning
There are a few reasons why a QR code might fail to scan:
- **Low Contrast:** Ensure there is high contrast between the QR code pattern and its background. The best practice is a dark foreground on a white background. Never use a light color on a light background.
- **Overcrowded Design:** If there are graphic elements or text too close to the code, it may interfere with the "Quiet Zone" (the border around the QR code). Always leave a clear margin around the QR code block.
- **Physical Size:** Ensure the printed code is large enough to scan. A minimum size of 2 x 2 cm (0.8 x 0.8 inches) is recommended for close-range scanning.

## Link Destination Has Changed
If your code links to an old URL or a 404 page:
- **Check Code Type:** Determine if your QR code is Static or Dynamic. Only Dynamic QR codes allow the destination link to be changed later without changing the physical pattern of the QR code itself.
- **How to Update:** Go to the links dashboard, locate the expired or outdated link, edit the "Destination URL" field, and save your changes. Your physical QR code will now redirect to the new destination.

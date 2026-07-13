# Ticket PDF con QR — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the PNG download in `view_qr.php` with a PDF ticket download that includes logos, QR code, student info, and event details in a designed layout.

**Architecture:** Client-side only. The existing PHP fetches student data and QR token from the API. The QR is rendered on a `<canvas>` by `qrcodejs`. A hidden HTML template holds the ticket design. On click, JavaScript captures the canvas as image, injects it into the template, and converts to PDF via `html2pdf.js`.

**Tech Stack:** `html2pdf.js` v0.10.2 (CDN), `qrcodejs` (already loaded), PHP (existing), Bootstrap 5 (existing)

## Global Constraints

- Single file modification: `FrontEnd/view_qr.php`
- No backend changes (API, routes, controllers untouched)
- QR token content stays the same (32-char hex string, not a URL)
- Download filename: `mi_pase_qr.pdf`
- Logos to include: `img/logouas.png`, `img/logofimaz.png`
- Brand colors: navy `#003B71`, gold `#D4AF37`/`#FDC800`
- No new CSS files — all ticket styles inline (required by html2pdf)
- Keep the existing `<canvas>` QR generation code intact

---

### Task 1: Add html2pdf.js and create ticket HTML template

**Files:**
- Modify: `FrontEnd/view_qr.php` (add CDN script + hidden ticket template)

- [ ] **Step 1: Add html2pdf.js CDN before `</body>`**

Add this line before the closing `</body>` tag in `view_qr.php`, after the existing `<script>` block:

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.2/html2pdf.bundle.min.js" integrity="sha512-5EJwY71EN4A3x5OYdpP2+OYvBxUbzH3CF5sYIOzTMk7kLB/7SIDlJLl7Y7tRP67iqRYVtXe3yJN4RrSFH4lX2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
```

- [ ] **Step 2: Add hidden ticket template inside the `<?php if (!isset($error)):` block**

Insert this HTML right after the closing `</div>` of `.card-body` and before the `<?php endif; ?>`:

```html
<!-- Template oculto para PDF -->
<div id="ticket-content" style="display:none; width: 400px; font-family: 'Segoe UI', Arial, sans-serif; background: #ffffff; border: 4px solid #D4AF37; border-radius: 12px; overflow: hidden; text-align: center;">
  <div style="background: #003B71; color: #FDC800; padding: 20px 16px 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
      <img src="img/logouas.png" alt="UAS" style="height: 50px;">
      <img src="img/logofimaz.png" alt="FIMAZ" style="height: 50px;">
    </div>
    <h2 style="margin: 8px 0 4px; font-size: 18px; font-weight: 700; letter-spacing: 1px; color: #FDC800;">CEREMONIA DE GRADUACIÓN</h2>
    <p style="margin: 0; font-size: 13px; color: #FDC800; opacity: 0.9;">15 de Julio de 2026</p>
  </div>

  <div style="padding: 24px 16px;">
    <div style="background: #fff; display: inline-block; padding: 12px; border-radius: 8px; border: 2px solid #D4AF37;">
      <img id="qr-ticket-img" src="" alt="QR" style="width: 180px; height: 180px;">
    </div>

    <h3 id="ticket-nombre" style="margin: 16px 0 4px; font-size: 20px; color: #003B71; font-weight: 600;"></h3>
    <p id="ticket-asiento" style="margin: 4px 0; font-size: 15px; color: #003B71; font-weight: 500;"></p>
    <p id="ticket-carrera" style="margin: 4px 0 8px; font-size: 13px; color: #555;"></p>
    <p id="ticket-horario" style="margin: 0; font-size: 12px; color: #888;"></p>
  </div>

  <div style="border-top: 2px solid #D4AF37; padding: 10px 16px; background: #003B71;">
    <p style="margin: 0; font-size: 11px; color: #FDC800;">Universidad Autónoma de Sinaloa</p>
  </div>
</div>
```

- [ ] **Step 3: Verify the page still renders correctly**
Load `view_qr.php` in the browser as a logged-in student. The page should display the same as before (QR code, name, seat, download button). The ticket template remains hidden.

---

### Task 2: Modify download button to generate PDF

**Files:**
- Modify: `FrontEnd/view_qr.php` (replace the click handler on `#downloadBtn`)

- [ ] **Step 1: Replace the existing download click handler**

Replace the existing `<script>` block that handles `downloadBtn` click (lines 126-133) with new code that captures the QR canvas, fills the template, and generates the PDF:

```javascript
document.getElementById("downloadBtn").addEventListener("click", function() {
  var canvas = document.querySelector("#qrcode canvas");
  var qrDataUrl = canvas.toDataURL("image/png");

  document.getElementById("qr-ticket-img").src = qrDataUrl;
  document.getElementById("ticket-nombre").textContent = "<?php echo htmlspecialchars($alumno['nombre']); ?>";
  document.getElementById("ticket-asiento").textContent = "Asiento: <?php echo $asignacionPublicada ? htmlspecialchars($alumno['asiento']) : 'No disponible'; ?>";
  document.getElementById("ticket-carrera").textContent = "<?php echo htmlspecialchars($alumno['carrera']); ?>";
  <?php
    $carrera_ticket = strtolower($alumno['carrera'] ?? '');
    $esLI = strpos($carrera_ticket, 'informática') !== false || strpos($carrera_ticket, 'informatica') !== false;
    $horario = $esLI ? '11:30 AM' : '10:00 AM';
  ?>
  document.getElementById("ticket-horario").textContent = "Horario: <?php echo $horario; ?>";

  var element = document.getElementById("ticket-content");
  var opt = {
    margin:       0,
    filename:     'mi_pase_qr.pdf',
    image:        { type: 'png', quality: 1 },
    html2canvas:  { scale: 2, useCORS: true, allowTaint: false },
    jsPDF:        { unit: 'mm', format: 'a5', orientation: 'portrait' }
  };
  html2pdf().set(opt).from(element).save();
});
```

**Important:** Remove the old `link.download = "mi_pase_qr.png"` and `link.href = canvas.toDataURL()` lines entirely.

- [ ] **Step 2: Verify PDF download works**
Load `view_qr.php` as a logged-in student whose group has QR enabled. Click "Descargar mi pase". Confirm `mi_pase_qr.pdf` downloads and displays: logos, QR code, student name, seat, career, event date, time, and UAS footer.

---

### Task 3: Final cleanup and verification

**Files:**
- Verify: `FrontEnd/view_qr.php`

- [ ] **Step 1: Remove leftover PNG download code**
Ensure no remaining code references `link.download = "mi_pase_qr.png"` or the old `link.click()` pattern.

- [ ] **Step 2: Final visual and functional check**
- Page loads without errors
- QR renders on screen
- PDF downloads correctly with all expected data
- PDF opens and displays the full ticket design

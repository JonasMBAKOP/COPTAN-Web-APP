<style>
/* ──────────────────────────────────────────────────────────────────────────
   CARTE SCOLAIRE — format élargi pour une meilleure lisibilité
   Dimensions: 95mm × 60mm
   ────────────────────────────────────────────────────────────────────────── */

.id-card {
    width: 95mm;
    height: 60mm;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    color: #191c1e;
    background: #ffffff;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border: 2px solid #1A3A6B;
    box-shadow: 0 1px 6px rgba(0,0,0,0.08);
}

.id-card__guilloche {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0.03;
    pointer-events: none;
    z-index: 0;
}

.id-card__content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 0.4mm;
}

/* HEADER BILINGUE ──────────────────────────────────────────────────────── */
.id-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2mm;
    padding: 1.4mm 2mm;
    background: #f8fafc;
    border-bottom: 1px solid #1A3A6B;
    flex-shrink: 0;
}

.id-card__header-section {
    font-size: 5.5pt;
    line-height: 1.12;
    font-weight: 700;
    text-transform: uppercase;
    color: #1A3A6B;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.id-card__header-fr,
.id-card__header-en {
    text-align: center;
}

.id-card__header-text {
    letter-spacing: 0.2px;
    font-size: 4.5pt;
}

.id-card__header-motto {
    font-size: 3.5pt;
    font-weight: 600;
    color: #6B7280;
    margin: 0.35mm 0;
    text-transform: none;
}

.id-card__flag {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.id-card__flag svg {
    width: 8mm;
    height: 5mm;
}

/* ÉCOLE INFO & TITRE ───────────────────────────────────────────────────── */
.id-card__school-header {
    padding: 0.8mm 2mm 0.6mm;
    background: #fff;
    text-align: center;
    border-bottom: 0.75px solid #e5e7eb;
    flex-shrink: 0;
}

.id-card__school-info {
    margin-bottom: 0.4mm;
}

.id-card__school-name {
    font-size: 7pt;
    font-weight: 900;
    color: #1A3A6B;
    line-height: 1.05;
    letter-spacing: 0.2px;
}

.id-card__school-acronym {
    font-size: 5.5pt;
    font-weight: 700;
    color: #E87722;
    margin-top: 0.2mm;
}

.id-card__title-section {
    background: rgba(26, 58, 107, 0.03);
}

.id-card__title {
    font-size: 5.5pt;
    font-weight: 900;
    text-transform: uppercase;
    color: #1A3A6B;
    letter-spacing: 0.3px;
    line-height: 1.1;
}

.id-card__subtitle {
    font-size: 3.5pt;
    color: #6B7280;
    margin-top: 0.3mm;
    font-weight: 600;
}

/* CORPS PRINCIPAL ──────────────────────────────────────────────────────── */
.id-card__body {
    flex: 1;
    display: flex;
    gap: 1mm;
    padding: 1.4mm 2mm 1.2mm;
    position: relative;
    overflow: hidden;
    min-height: 0;
}

/* PHOTO SECTION ────────────────────────────────────────────────────────── */
.id-card__photo-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5mm;
    flex-shrink: 0;
}

.id-card__photo-box {
    width: 22mm;
    height: 28mm;
    border: 1px solid #1A3A6B;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.id-card__photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.id-card__photo-placeholder {
    font-size: 9pt;
    font-weight: 900;
    color: #64748b;
}

.id-card__matricule {
    font-size: 4.5pt;
    font-weight: 700;
    color: #1A3A6B;
    text-align: center;
    white-space: nowrap;
}

/* INFO SECTION ────────────────────────────────────────────────────────── */
.id-card__info-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    min-width: 0;
}

.id-card__info-table {
    width: calc(100% - 20mm);
    border-collapse: collapse;
    font-size: 4.5pt;
    line-height: 1.12;
    table-layout: fixed;
}

.id-card__info-row {
    height: 5mm;
}

.id-card__info-label {
    font-weight: 700;
    color: #4B5563;
    width: 15mm;
    padding: 0.15mm 0.6mm 0.15mm 0;
    white-space: nowrap;
    vertical-align: top;
}

.id-card__label-fr,
.id-card__label-en {
    display: block;
}

.id-card__label-en {
    margin-top: 0.12mm;
    font-size: 3.8pt;
    font-style: italic;
    font-weight: 600;
    color: #6B7280;
}

.id-card__info-value {
    font-weight: 700;
    color: #111827;
    border-bottom: 0.5px solid #cbd5e1;
    padding: 0.25mm 0.4mm;
    word-break: break-word;
    vertical-align: middle;
}

.id-card__info-value--highlight {
    color: #E87722;
    font-weight: 900;
    text-transform: uppercase;
    font-size: 5pt;
}

/* LOGO & COORDONNÉES HAUT DROIT ─────────────────────────────────────────── */
.id-card__top-logo {
    position: absolute;
    top: -1.8mm;
    right: 0;
    width: 17mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: 0;
    text-align: center;
    line-height: 1;
}

.id-card__top-logo-image {
    width: 10.5mm;
    height: 10.5mm;
    object-fit: contain;
    display: block;
}

.id-card__top-logo-meta {
    font-size: 3.2pt;
    font-weight: 700;
    color: #1A3A6B;
    line-height: 1;
    white-space: nowrap;
    margin-top: -0.15mm;
}

.id-card__top-logo-meta + .id-card__top-logo-meta {
    margin-top: -0.05mm;
}

/* CACHET/SIGNATURE BAS DROIT ───────────────────────────────────────────── */
.id-card__seal-area {
    position: absolute;
    bottom: 0.5mm;
    right: 0.5mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3mm;
    width: 16mm;
    text-align: center;
}

.id-card__seal-image {
    width: 15mm;
    height: 15mm;
    object-fit: contain;
    transform: rotate(-12deg);
    opacity: 0.9;
}

.id-card__seal-svg {
    width: 17mm;
    height: 17mm;
    flex-shrink: 0;
    transform: rotate(-12deg);
}

.id-card__seal-label {
    font-size: 3.5pt;
    font-weight: 700;
    color: #374151;
    white-space: nowrap;
}

/* FOOTER STRIPE (DRAPEAU) ──────────────────────────────────────────────── */
.id-card__footer-stripe {
    height: 2.5mm;
    flex-shrink: 0;
    margin-top: auto;
}

.id-card__footer-stripe svg {
    display: block;
    width: 100%;
    height: 100%;
}

@media print {
    body {
        background: white;
        margin: 0;
        padding: 0;
    }

    .no-print {
        display: none !important;
    }

    .id-card {
        box-shadow: none;
        border: 2px solid #1A3A6B;
        margin: 0;
        page-break-inside: avoid;
    }
}
</style>

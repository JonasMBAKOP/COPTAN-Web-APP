<style>
/* ──────────────────────────────────────────────────────────────────────────
   CARTE SCOLAIRE - CR80 PORTRAIT (210 × 297 mm réduit)
   Dimensions effectives: ~105 × 148.5 mm
   ────────────────────────────────────────────────────────────────────────── */

.id-card {
    width: 105mm;
    max-width: 105mm;
    aspect-ratio: 85.6 / 154;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #1a2e4a;
    background: #fff;
    border: 2px solid #1A3A6B;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* HEADER BILINGUE ──────────────────────────────────────────────────────── */
.id-card__header {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 8px;
    padding: 6px 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
}

.id-card__header-section {
    font-size: 6px;
    line-height: 1.2;
    font-weight: 700;
    text-transform: uppercase;
}

.id-card__header-fr {
    text-align: left;
    color: #1A3A6B;
}

.id-card__header-en {
    text-align: right;
    color: #1A3A6B;
}

.id-card__header-text {
    letter-spacing: 0.5px;
}

.id-card__header-motto {
    font-size: 5.5px;
    font-weight: 600;
    color: #6B7280;
    margin-top: 1px;
}

.id-card__flag {
    font-size: 14px;
    line-height: 1;
}

/* SECTION ÉCOLE ───────────────────────────────────────────────────────── */
.id-card__school-section {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 8px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
}

.id-card__logo-container {
    flex-shrink: 0;
}

.id-card__logo {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

.id-card__logo-placeholder {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #1A3A6B;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 12px;
}

.id-card__school-info {
    flex: 1;
    text-align: center;
}

.id-card__school-name {
    font-size: 12px;
    font-weight: 900;
    color: #1A3A6B;
    line-height: 1.05;
    letter-spacing: 0.3px;
}

.id-card__school-acronym {
    font-size: 7px;
    font-weight: 700;
    color: #d97706;
    margin-top: 1px;
}

/* TITRE DOCUMENT ───────────────────────────────────────────────────────── */
.id-card__title-section {
    padding: 4px 8px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
}

.id-card__title {
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
    color: #1A3A6B;
    letter-spacing: 0.4px;
    line-height: 1.15;
}

.id-card__subtitle {
    font-size: 6px;
    color: #6B7280;
    margin-top: 2px;
    font-weight: 600;
}

/* CORPS PRINCIPAL ──────────────────────────────────────────────────────── */
.id-card__body {
    flex: 1;
    display: grid;
    grid-template-columns: 42px 1fr;
    gap: 6px;
    padding: 6px 8px;
    overflow: hidden;
}

/* COLONNE GAUCHE - PHOTO ───────────────────────────────────────────────── */
.id-card__left {
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: center;
}

.id-card__photo-box {
    width: 40px;
    height: 50px;
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
    font-size: 14px;
    font-weight: 900;
    color: #64748b;
}

.id-card__matricule {
    font-size: 6px;
    font-weight: 700;
    color: #1A3A6B;
    text-align: center;
    width: 100%;
}

/* COLONNE DROITE - INFOS ───────────────────────────────────────────────── */
.id-card__right {
    display: flex;
    flex-direction: column;
    position: relative;
}

.id-card__info-table {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.id-card__info-row {
    display: grid;
    grid-template-columns: auto auto 1fr;
    gap: 4px;
    align-items: center;
    font-size: 7px;
    line-height: 1.15;
}

.id-card__info-label {
    font-weight: 700;
    color: #4B5563;
    text-align: left;
    white-space: nowrap;
}

.id-card__info-value {
    font-weight: 700;
    color: #111827;
    border-bottom: 0.5px solid #cbd5e1;
    padding-bottom: 1px;
    word-break: break-word;
    text-align: left;
}

.id-card__info-value--highlight {
    color: #d97706;
    font-size: 7px;
    text-transform: uppercase;
}

/* LOGO ÉCOLE HAUT DROIT ────────────────────────────────────────────────── */
.id-card__top-right-logo {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 24px;
    height: 24px;
}

.id-card__top-right-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* CACHET/SIGNATURE BAS DROIT ───────────────────────────────────────────── */
.id-card__bottom-right-seal {
    position: absolute;
    bottom: 2px;
    right: 2px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
}

.id-card__seal-image {
    width: 28px;
    height: 28px;
    object-fit: contain;
}

.id-card__seal-label {
    font-size: 5px;
    font-weight: 700;
    color: #374151;
    text-align: center;
    white-space: nowrap;
}

/* BANDE COULEUR (DRAPEAU CAMEROUN) ─────────────────────────────────────── */
.id-card__footer-stripe {
    height: 3px;
    background: linear-gradient(to right, #00B050 0%, #00B050 33.33%, #CE1126 33.33%, #CE1126 66.66%, #FCD116 66.66%, #FCD116 100%);
    flex-shrink: 0;
}

/* RESPONSIVE ────────────────────────────────────────────────────────────── */
@media (max-width: 600px) {
    .id-card {
        max-width: 100%;
    }
}
</style>

<style>
    :root {
        --bleu:      #4A86C8;
        --bleu-dark: #1A3A6B;
        --vert:      #1A5C2A;
        --or:        #C8A415;
        --rouge:     #DC2626;
        --gris:      #F8FAFC;
    }

    @page {
        size: A4 portrait;
        margin: 6mm 8mm;
    }

    * { box-sizing: border-box; }

    html, body {
        margin: 0; padding: 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #111827;
    }

    body { background: #E5E7EB; }

    .livret-page {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 6mm 8mm;
        background: white;
        position: relative;
        overflow: hidden;
    }

    @media print {
        body { background: white; }
        .livret-page { page-break-after: always; }
        .livret-page:last-child { page-break-after: auto; }
        .no-print { display: none !important; }
    }

    /* ── EN-TÊTE OFFICIEL ──────────────────────────────────────── */
    .cert-official-header {
        margin-bottom: 4px;
    }

    .cert-official-header__columns {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 8px;
        align-items: start;
        /* border-bottom: 2px solid var(--bleu); */
        padding-bottom: 4px;
    }

    .cert-official-header__side {
        display: flex;
        flex-direction: column;
        gap: 1px;
        font-size: 8px;
    }

    .cert-official-header__side--fr { text-align: center; align-items: center; }
    .cert-official-header__side--en { text-align: center; align-items: center; }

    .cert-official-header__republic {
        font-size: 8.5px; font-weight: 900; text-transform: uppercase;
        letter-spacing: .04em; color: #111827;
    }
    .cert-official-header__motto    { font-size: 7.5px; font-style: italic; color: #374151; }
    .cert-official-header__stars    { font-size: 6.5px; color: var(--bleu); letter-spacing: 2px; }
    .cert-official-header__ministry { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #374151; }
    .cert-official-header__school   { font-size: 8.5px; font-weight: 900; text-transform: uppercase; color: var(--bleu-dark); }
    .cert-official-header__meta     { font-size: 7px; color: #6B7280; }
    .cert-official-header__email    { font-size: 7px; color: #6B7280; }
    .cert-official-header__email span { font-weight: 600; }

    .cert-official-header__logo {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cert-official-header__logo img {
        width: 22mm; height: 22mm; object-fit: contain;
    }

    .cert-official-header__logo-placeholder {
        width: 22mm; height: 22mm;
        border: 2px solid var(--bleu);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 900; color: var(--bleu);
    }

    .cert-official-header__agreements {
        text-align: center;
        font-size: 6.5px; color: #6B7280;
        margin: 2px 0;
        display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;
    }

    .cert-official-header__title {
        text-align: center;
        font-size: 12px; font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--bleu-dark);
        border: 2px solid var(--bleu);
        display: inline-block;
        margin: 3px auto;
        padding: 4px 14px;
        border-radius: 3px;
        width: 100%;
    }

    /* ── INFOS ÉLÈVE ──────────────────────────────────────────── */
    .student-info-block {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 6px;
        margin-bottom: 4px;
        align-items: start;
    }

    /* ── TABLEAU DES NOTES ────────────────────────────────────── */
    .livret-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7px;
        margin-bottom: 4px;
    }

    .livret-table th, .livret-table td {
        border: 1px solid #9CA3AF;
        padding: 2px 3px;
        text-align: center;
        vertical-align: middle;
    }

    .livret-table th {
        background: rgba(74, 134, 200, 0.92);
        color: #ffffff;
        font-weight: 700;
        font-size: 6.5px;
        text-transform: uppercase;
    }

    .livret-table th.th-trim {
        background: rgba(26, 58, 107, 0.85);
    }

    .livret-table th.th-annual {
        background: rgba(55, 65, 81, 0.92);
    }

    .livret-table td.subject-cell {
        text-align: left;
        padding: 2px 4px;
        vertical-align: top;
        min-width: 60px;
    }

    .subject-name {
        font-weight: 700; font-size: 7px; color: #111827;
        display: block; line-height: 1.2;
    }

    .subject-coef {
        font-size: 6px; color: #6B7280; font-weight: 600;
        display: block;
    }

    .livret-table tr.category-row td {
        background: rgba(74, 134, 200, 0.08);
        font-weight: 900;
        text-transform: uppercase;
        color: var(--bleu-dark);
        text-align: left;
        padding: 2px 6px;
        font-size: 7px;
    }

    .livret-table tr.total-row td {
        background: #EFF6FF;
        font-weight: 800;
        font-size: 6.5px;
    }

    .livret-table tr.footer-row td {
        background: #F1F5F9;
        font-weight: 900;
        font-size: 7px;
    }

    .grade-good   { color: #1A5C2A; font-weight: 700; }
    .grade-avg    { color: #92400E; font-weight: 700; }
    .grade-bad    { color: #DC2626; font-weight: 700; }
    .grade-absent { color: #6B7280; font-style: italic; }

    /* ── STATS BAS ───────────────────────────────────────────── */
    .livret-bottom {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 6px;
        margin-top: 4px;
    }

    .stat-box {
        border: 1px solid #CBD5E1;
        border-radius: 3px;
        padding: 4px 6px;
        background: #F8FAFC;
        font-size: 7px;
    }

    .stat-box-title {
        text-align: center;
        font-size: 6.5px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--bleu-dark);
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 2px;
        margin-bottom: 3px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 1px;
    }

    .stat-label {
        font-weight: 600;
        color: #374151;
        font-size: 6.5px;
    }

    .stat-label em {
        display: block;
        font-style: italic;
        font-size: 5.5px;
        color: #9CA3AF;
        font-weight: normal;
    }

    .stat-value {
        font-weight: 900;
        font-size: 8px;
        color: var(--bleu-dark);
    }

    /* Appréciations */
    .appr-codes-row {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .appr-code-cell {
        border: 1px solid #CBD5E1;
        border-radius: 3px;
        overflow: hidden;
        flex: 1;
        min-width: 50px;
        text-align: center;
    }

    .appr-code-cell .code {
        background: var(--bleu-dark);
        color: #fff;
        font-weight: 900;
        font-size: 7px;
        padding: 1px 3px;
    }

    .appr-code-cell .meaning {
        font-size: 5.5px;
        color: #374151;
        padding: 2px 3px;
        line-height: 1.2;
    }

    .appr-code-cell .meaning em { color: #9CA3AF; }

    /* Signatures */
    .signature-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        border-top: 1px dashed #D1D5DB;
        padding-top: 4px;
        margin-top: 4px;
    }

    .sig-box { text-align: center; font-size: 6.5px; }

    .sig-label {
        font-weight: 900;
        text-transform: uppercase;
        color: var(--bleu-dark);
        font-size: 7px;
        margin-bottom: 12px;
    }

    .sig-line {
        border-top: 1px solid #D1D5DB;
        padding-top: 2px;
        color: #9CA3AF;
        font-size: 6px;
    }

    .livret-footer {
        border-top: 1px solid #E5E7EB;
        margin-top: 4px;
        padding-top: 2px;
        display: flex;
        justify-content: space-between;
        font-size: 6px;
        color: #9CA3AF;
    }

    /* ── BARRE D'OUTILS (preview uniquement) ─────────────────── */
    .toolbar {
        position: fixed; top: 0; left: 0; right: 0;
        background: #1E3A5F; color: #fff;
        padding: 8px 16px;
        display: flex; align-items: center; gap: 12px;
        z-index: 999; font-size: 13px;
    }

    .toolbar button {
        background: #4A86C8; color: #fff;
        border: none; border-radius: 6px;
        padding: 5px 14px; cursor: pointer;
        font-size: 12px; font-weight: 700;
        transition: background .15s;
    }

    .toolbar button:hover { background: #1A3A6B; }

    body.has-toolbar { padding-top: 40px; }
</style>

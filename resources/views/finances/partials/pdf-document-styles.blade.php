{{-- Styles communs — documents financiers PDF / impression A4 --}}
<style>
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        background: #fff;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @media screen {
        body {
            background: #E5E7EB;
            padding: 10mm 0;
        }
    }

    @media print {
        body {
            background: #fff !important;
            padding: 0 !important;
        }
        .no-print { display: none !important; }
    }

    .page.cert-page,
    .pdf-document {
        width: 100%;
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        min-height: auto !important;
        box-sizing: border-box;
        background: #fff;
    }

    @media screen {
        .page.cert-page,
        .pdf-document {
            width: 210mm;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }
    }

    .cert-official-header {
        margin-bottom: 8px !important;
        padding-bottom: 6px !important;
    }

    .cert-official-header__columns {
        width: 100%;
    }
</style>

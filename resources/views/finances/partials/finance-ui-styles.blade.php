<style>
@keyframes finFadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
.fin-kpi { animation: finFadeUp .42s ease both; transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease; }
.fin-kpi:hover { box-shadow: 0 10px 32px rgba(26,58,107,.1); transform: translateY(-2px); border-color: #D1D5DB; }
.fin-panel { animation: finFadeUp .42s ease both; transition: box-shadow .2s ease, border-color .2s ease; }
.fin-panel:hover { border-color: #E5E7EB; }
.fin-progress { transition: width .85s cubic-bezier(.22,.68,0,1.12); }
.fin-bar { transition: height 1.4s cubic-bezier(.16,1,.3,1); }
.fin-row { transition: background .15s ease; }
.fin-row:hover { background: #F9FAFB; }
.fin-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    /* min-width: 200px; */
    white-space: nowrap;
    gap: .5rem;
    transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease, opacity .16s ease;
}
.fin-action svg { flex: 0 0 auto; }
.fin-action:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(26,58,107,.08); }
.fin-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 999px;
    border: 1px solid #E5E7EB;
    background: #F9FAFB;
    padding: .3rem .65rem;
    font-size: 11px;
    font-weight: 700;
    color: #1A3A6B;
}
.fin-input,
.fin-select {
    width: 100%;
    border: 1px solid #E5E7EB;
    border-radius: .75rem;
    background: #fff;
    padding: .65rem .85rem;
    font-size: .875rem;
    font-weight: 600;
    color: #374151;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.fin-input:focus,
.fin-select:focus {
    outline: none;
    border-color: #1A3A6B;
    box-shadow: 0 0 0 3px rgba(26,58,107,.1);
}
.fin-segment {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    padding: .4rem;
    border-radius: 1rem;
    border: 1px solid #E2E8F0;
    background: #F8FAFC;
}
.type-btn {
    flex: 1 1 auto;
    min-width: 95px;
    border: 1px solid transparent;
    cursor: pointer;
    border-radius: .75rem;
    padding: .55rem .95rem;
    font-size: .75rem;
    font-weight: 700;
    color: #64748B;
    background: #ffffff;
    border-color: #E2E8F0;
    transition: all .2s ease;
    white-space: nowrap;
    text-align: center;
}
.type-btn.active {
    background: #1A3A6B !important;
    border-color: #1A3A6B !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(26, 58, 107, 0.22);
}
.type-btn:not(.active):hover {
    background: #EBF3FB;
    border-color: #BFDBFE;
    color: #1A3A6B;
}


.fin-table thead th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9CA3AF;
}
.fin-table tbody td { font-size: .875rem; font-weight: 600; color: #374151; }
.fin-empty {
    display: flex;
    min-height: 180px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    border: 1px dashed #E5E7EB;
    background: #F9FAFB;
    padding: 2rem;
    text-align: center;
}
@media print { .no-print { display: none !important; } }
</style>

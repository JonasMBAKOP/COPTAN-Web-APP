<div class="fiche-principal-signature fiche-principal-signature--tight">
    <div>Le Principal</div>
    <div class="fiche-principal-signature__en">The Principal</div>
    @if($school->signature_seal)
        <div class="fiche-principal-signature__seal">
            <img src="{{ asset('storage/' . $school->signature_seal) }}" alt="Cachet du Principal">
        </div>
    @endif
</div>

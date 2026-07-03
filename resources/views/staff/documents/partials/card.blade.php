<div class="id-card">
    <div class="id-card__content">
        <div class="id-card__header">
            <div class="id-card__header-section id-card__header-fr">
                {{ config('app.name') }}
            </div>
            <div class="id-card__header-section id-card__header-en">
                CARTE PROFESSIONNELLE
            </div>
        </div>

        <div class="id-card__school-header">
            <div class="id-card__school-name">{{ config('app.name') }}</div>
            <div class="id-card__school-acronym">{{ config('app.short_name') ?? '' }}</div>
        </div>

        <div class="id-card__body">
            <div class="id-card__photo-section">
                <div class="id-card__photo-box">
                    @if($staff->photo)
                        <img src="{{ asset('storage/' . $staff->photo) }}" class="id-card__photo" alt="photo">
                    @else
                        <div class="id-card__photo-placeholder">{{ strtoupper(substr($staff->first_name ?? $staff->full_name, 0, 2)) }}</div>
                    @endif
                </div>
                <div class="id-card__matricule">{{ $staff->employee_number ?? '' }}</div>
            </div>

            <div class="id-card__info-section">
                <table class="id-card__info-table">
                    <tr class="id-card__info-row">
                        <td class="id-card__info-label">Nom</td>
                        <td class="id-card__info-value">{{ $staff->full_name }}</td>
                    </tr>
                    <tr class="id-card__info-row">
                        <td class="id-card__info-label">Poste</td>
                        <td class="id-card__info-value">{{ $staff->position?->name }}</td>
                    </tr>
                    <tr class="id-card__info-row">
                        <td class="id-card__info-label">Tél.</td>
                        <td class="id-card__info-value">{{ $staff->phone }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

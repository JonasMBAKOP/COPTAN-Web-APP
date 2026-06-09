<?php

namespace App\Http\Controllers;

use App\Models\SchoolAgreement;
use App\Models\SchoolPhone;
use App\Models\SchoolSetting;
use App\Models\AuditLog;
use App\Http\Requests\UpdateSchoolSettingRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    use AuthorizesRequests;
    /**
     * Affiche les paramètres de l'établissement.
     */
    public function index()
    {
        $settings   = SchoolSetting::instance();
        $phones     = SchoolPhone::orderByDesc('is_primary')
                                  ->orderBy('id')->get();
        $agreements = SchoolAgreement::orderBy('cycle')->get();

        return view('settings.index',
            compact('settings', 'phones', 'agreements'));
    }

    // ── MISE À JOUR DES INFORMATIONS GÉNÉRALES ────────────────────────────
    public function update(UpdateSchoolSettingRequest $request)
    {
        $settings  = SchoolSetting::instance();
        $oldValues = $settings->toArray();

        $settings->update($request->validated());

        AuditLog::log('updated', $settings, $oldValues, $settings->toArray());

        return back()
            ->with('success', 'Paramètres de l\'établissement mis à jour.')
            ->with('active_tab', 'general');
    }

    // ── LOGO — UPLOAD ─────────────────────────────────────────────────────
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image',
                       'mimes:jpg,jpeg,png,svg',
                       'max:2048'],
        ], [
            'logo.required' => 'Veuillez sélectionner une image.',
            'logo.image'    => 'Le fichier doit être une image.',
            'logo.mimes'    => 'Formats acceptés : JPG, PNG, SVG.',
            'logo.max'      => 'La taille maximale est 2 Mo.',
        ]);

        $settings = SchoolSetting::instance();

        // Supprimer l'ancien logo s'il existe
        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
        }

        // Enregistrer le nouveau logo
        $path = $request->file('logo')->store('school', 'public');
        $settings->update(['logo' => $path]);

        AuditLog::log('logo_updated', $settings);

        return back()
            ->with('success', 'Logo mis à jour avec succès.')
            ->with('active_tab', 'appearance');
    }

    // ── LOGO — SUPPRESSION ────────────────────────────────────────────────
    public function deleteLogo()
    {
        $settings = SchoolSetting::instance();

        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
        }

        $settings->update(['logo' => null]);
        AuditLog::log('logo_deleted', $settings);

        return back()
            ->with('success', 'Logo supprimé.')
            ->with('active_tab', 'appearance');
    }

    // ── TÉLÉPHONES — CRÉATION ─────────────────────────────────────────────
    public function storePhone(Request $request)
    {
        $request->validate([
            'number' => ['required', 'string', 'max:30'],
            'label'  => ['nullable', 'string', 'max:50'],
        ], [
            'number.required' => 'Le numéro de téléphone est obligatoire.',
        ]);

        // Si premier numéro ou marqué comme principal
        $isFirst = SchoolPhone::count() === 0;

        SchoolPhone::create([
            'number'     => $request->number,
            'label'      => $request->label,
            'is_primary' => $isFirst || $request->boolean('is_primary'),
        ]);

        // S'assurer qu'il n'y a qu'un seul numéro principal
        if ($request->boolean('is_primary')) {
            SchoolPhone::where('id', '!=',
                SchoolPhone::latest()->first()?->id)
                ->update(['is_primary' => false]);
        }

        return back()
            ->with('success', 'Numéro ajouté.')
            ->with('active_tab', 'phones');
    }

    // ── TÉLÉPHONES — MODIFICATION ─────────────────────────────────────────
    public function updatePhone(Request $request, SchoolPhone $phone)
    {
        $request->validate([
            'number' => ['required', 'string', 'max:30'],
            'label'  => ['nullable', 'string', 'max:50'],
        ]);

        $phone->update([
            'number'     => $request->number,
            'label'      => $request->label,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        if ($request->boolean('is_primary')) {
            SchoolPhone::where('id', '!=', $phone->id)
                       ->update(['is_primary' => false]);
        }

        return back()
            ->with('success', 'Numéro mis à jour.')
            ->with('active_tab', 'phones');
    }

    // ── TÉLÉPHONES — SUPPRESSION ──────────────────────────────────────────
    public function destroyPhone(SchoolPhone $phone)
    {
        $phone->delete();

        // Si c'était le principal, définir le premier restant comme principal
        if ($phone->is_primary) {
            SchoolPhone::first()?->update(['is_primary' => true]);
        }

        return back()
            ->with('success', 'Numéro supprimé.')
            ->with('active_tab', 'phones');
    }

    // ── TÉLÉPHONES — DÉFINIR COMME PRINCIPAL ─────────────────────────────
    public function setPrimaryPhone(SchoolPhone $phone)
    {
        SchoolPhone::query()->update(['is_primary' => false]);
        $phone->update(['is_primary' => true]);

        return back()
            ->with('success', "{$phone->number} défini comme numéro principal.")
            ->with('active_tab', 'phones');
    }

    // ── AGRÉMENTS — CRÉATION ─────────────────────────────────────────────
    public function storeAgreement(Request $request)
    {
        $request->validate([
            'number'      => ['required', 'string', 'max:100'],
            'cycle'       => ['required', 'in:premier_cycle,second_cycle,autre'],
            'label'       => ['nullable', 'string', 'max:200'],
            'issued_date' => ['nullable', 'date'],
        ], [
            'number.required' => 'Le numéro d\'agrément est obligatoire.',
            'cycle.required'  => 'Le cycle est obligatoire.',
        ]);

        SchoolAgreement::create($request->only(
            'number', 'cycle', 'label', 'issued_date'
        ));

        return back()
            ->with('success', 'Numéro d\'agrément ajouté.')
            ->with('active_tab', 'agreements');
    }

    // ── AGRÉMENTS — MODIFICATION ──────────────────────────────────────────
    public function updateAgreement(Request $request, SchoolAgreement $agreement)
    {
        $request->validate([
            'number'      => ['required', 'string', 'max:100'],
            'cycle'       => ['required', 'in:premier_cycle,second_cycle,autre'],
            'label'       => ['nullable', 'string', 'max:200'],
            'issued_date' => ['nullable', 'date'],
        ]);

        $agreement->update($request->only(
            'number', 'cycle', 'label', 'issued_date'
        ));

        return back()
            ->with('success', 'Agrément mis à jour.')
            ->with('active_tab', 'agreements');
    }

    // ── AGRÉMENTS — SUPPRESSION ───────────────────────────────────────────
    public function destroyAgreement(SchoolAgreement $agreement)
    {
        $agreement->delete();

        return back()
            ->with('success', 'Agrément supprimé.')
            ->with('active_tab', 'agreements');
    }

    // /**
    //  * Affiche le formulaire d'édition des paramètres.
    //  */
    // public function edit()
    // {
    //     $this->authorize('update', SchoolSetting::class);

    //     $setting = SchoolSetting::instance();

    //     return view('settings.school.edit', compact('setting'));
    // }

    // /**
    //  * Met à jour les paramètres de l'établissement.
    //  */
    // public function update(UpdateSchoolSettingRequest $request)
    // {
    //     $this->authorize('update', SchoolSetting::class);

    //     $setting = SchoolSetting::instance();

    //     $data = $request->validated();

    //     // Gestion de l'upload du logo
    //     if ($request->hasFile('logo')) {
    //         // Supprimer l'ancien logo s'il existe
    //         if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
    //             Storage::disk('public')->delete($setting->logo);
    //         }

    //         // Stocker le nouveau logo
    //         $data['logo'] = $request->file('logo')->store('logos', 'public');
    //     }

    //     // Mettre à jour les paramètres
    //     $setting->update($data);

    //     return redirect()
    //         ->route('settings.school.edit')
    //         ->with('success', 'Les paramètres de l\'établissement ont été mis à jour avec succès.');
    // }

    // /**
    //  * Récupère les informations de l'établissement (API)
    //  */
    // public function getInfo()
    // {
    //     $setting = SchoolSetting::instance();

    //     return response()->json([
    //         'full_name'  => $setting->full_name,
    //         'short_name' => $setting->short_name,
    //         'logo'       => $setting->logo ? asset('storage/' . $setting->logo) : null,
    //         'email'      => $setting->email,
    //         'website'    => $setting->website,
    //     ]);
    // }
}

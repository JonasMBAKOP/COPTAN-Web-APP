<?php

namespace App\Http\Controllers;

use App\Models\SchoolPhone;
use App\Models\SchoolSetting;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffDocumentController extends Controller
{
    public function bulkCards()
    {
        $school = SchoolSetting::instance();
        $phones = SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();
        $staff = Staff::orderBy('last_name')->get();
        return view('staff.documents.cards', compact('staff', 'school', 'phones'));
    }

    public function singleCard(Staff $staff)
    {
        $school = SchoolSetting::instance();
        $phones = SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();
        return view('staff.documents.card', compact('staff', 'school', 'phones'));
    }

    public function printBulk(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Sélectionnez au moins un membre du personnel pour imprimer.');
        }

        $school = SchoolSetting::instance();
        $phones = SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();
        $staff = Staff::whereIn('id', $ids)->orderBy('last_name')->get();
        return view('staff.documents.cards-print', compact('staff', 'school', 'phones'));
    }
}

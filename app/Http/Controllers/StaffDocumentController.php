<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffDocumentController extends Controller
{
    public function bulkCards()
    {
        $staff = Staff::orderBy('last_name')->get();
        return view('staff.documents.cards', compact('staff'));
    }

    public function singleCard(Staff $staff)
    {
        return view('staff.documents.card', compact('staff'));
    }

    public function printBulk(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Sélectionnez au moins un membre du personnel pour imprimer.');
        }

        $staff = Staff::whereIn('id', $ids)->orderBy('last_name')->get();
        return view('staff.documents.cards-print', compact('staff'));
    }
}

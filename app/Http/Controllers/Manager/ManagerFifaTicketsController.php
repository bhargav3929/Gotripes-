<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\FifaMatch;
use App\Models\FifaSetting;
use App\Models\FifaTicket;
use App\Models\FifaTicketRequest;
use Illuminate\Http\Request;

class ManagerFifaTicketsController extends Controller
{
    /** Dashboard: markup setting + all matches with their ticket inventory. */
    public function index()
    {
        $setting   = FifaSetting::current();
        $matches   = FifaMatch::with('tickets')->orderBy('sort_order')->orderBy('id')->get();
        $newCount  = FifaTicketRequest::where('status', 'new')->count();

        return view('manager.fifa.index', compact('setting', 'matches', 'newCount'));
    }

    /** Update the global profit margin. */
    public function updateMarkup(Request $request)
    {
        $request->validate(['markup_percent' => 'required|numeric|min:0|max:1000']);

        FifaSetting::current()->update([
            'markup_percent' => $request->markup_percent,
        ]);

        return back()->with('success', 'Profit margin updated to ' . rtrim(rtrim(number_format($request->markup_percent, 2), '0'), '.') . '%.');
    }

    public function storeMatch(Request $request)
    {
        $data = $request->validate([
            'match_code' => 'required|string|max:20',
            'team_a'     => 'required|string|max:120',
            'team_b'     => 'required|string|max:120',
            'stage'      => 'required|string|max:60',
            'match_date' => 'nullable|date',
            'venue'      => 'nullable|string|max:160',
        ]);
        $data['sort_order'] = (int) preg_replace('/\D/', '', $data['match_code']);
        $data['is_active']  = true;

        FifaMatch::create($data);

        return back()->with('success', 'Match added.');
    }

    public function updateMatch(Request $request, $id)
    {
        $match = FifaMatch::findOrFail($id);
        $data = $request->validate([
            'team_a'     => 'required|string|max:120',
            'team_b'     => 'required|string|max:120',
            'stage'      => 'required|string|max:60',
            'match_date' => 'nullable|date',
            'venue'      => 'nullable|string|max:160',
            'is_active'  => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $match->update($data);

        return back()->with('success', 'Match updated.');
    }

    public function destroyMatch($id)
    {
        FifaMatch::findOrFail($id)->delete(); // cascades to tickets

        return back()->with('success', 'Match and its tickets removed.');
    }

    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'match_id'       => 'required|exists:fifa_matches,id',
            'quantity'       => 'required|integer|min:1|max:50',
            'category'       => 'required|string|max:30',
            'block'          => 'nullable|string|max:30',
            'seat_row'       => 'nullable|string|max:30',
            'supplier_price' => 'required|numeric|min:0',
        ]);
        $data['is_active'] = true;

        FifaTicket::create($data);

        return back()->with('success', 'Ticket listing added.');
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = FifaTicket::findOrFail($id);
        $data = $request->validate([
            'quantity'       => 'required|integer|min:1|max:50',
            'category'       => 'required|string|max:30',
            'block'          => 'nullable|string|max:30',
            'seat_row'       => 'nullable|string|max:30',
            'supplier_price' => 'required|numeric|min:0',
            'is_active'      => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $ticket->update($data);

        return back()->with('success', 'Ticket listing updated.');
    }

    public function destroyTicket($id)
    {
        FifaTicket::findOrFail($id)->delete();

        return back()->with('success', 'Ticket listing removed.');
    }

    /** Customer request inbox. */
    public function requests()
    {
        $requests = FifaTicketRequest::with('match')->orderByDesc('id')->paginate(30);

        return view('manager.fifa.requests', compact('requests'));
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:new,contacted,closed']);
        FifaTicketRequest::findOrFail($id)->update(['status' => $request->status]);

        return back()->with('success', 'Request status updated.');
    }
}

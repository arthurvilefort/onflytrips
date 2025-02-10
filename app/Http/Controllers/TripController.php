<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Http\Requests\TripFilterRequest;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        $trip = Trip::create([
            'user_id' => auth()->id(),
            'requester_name' => auth()->user()->name,
            'destination' => $validated['destination'],
            'departure_date' => $validated['departure_date'],
            'return_date' => $validated['return_date'],
            'status' => 'solicitado',
        ]);

        return response()->json($trip, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->user_id === auth()->id()) {
            return response()->json(['error' => 'Usuário não pode aprovar/cancelar seu próprio pedido.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:aprovado,cancelado',
        ]);

        $trip->update(['status' => $validated['status']]);

        return response()->json($trip);
    }

    public function show($id)
    {
        $trip = Trip::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($trip);
    }

    public function index(TripFilterRequest $request)
    {
        $query = Trip::query()->where('user_id', auth()->id());

        if ($request->destination) {
            $query->where('destination', 'LIKE', '%' . $request->destination . '%');
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }



    public function cancel($id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->status !== 'aprovado') {
            return response()->json(['error' => 'Apenas pedidos aprovados podem ser cancelados.'], 400);
        }

        $trip->update(['status' => 'cancelado']);
        return response()->json(['message' => 'Pedido cancelado com sucesso.']);
    }
}

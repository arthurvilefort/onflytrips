<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Http\Requests\TripFilterRequest;
use Carbon\Carbon;
use App\Notifications\TripStatusUpdated;
use Illuminate\Support\Facades\Notification;



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
            return response()->json(['error' => 'Usuário não pode alterar seu próprio pedido.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:aprovado,solicitado',
        ]);

        $trip->update(['status' => $validated['status']]);

        $user = $trip->user;
        $user->notify(new TripStatusUpdated($trip));


        return response()->json($trip);
    }

    public function show($id)
    {
        $trip = Trip::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($trip);
    }

    public function index(Request $request)
    {
        $query = Trip::query();

        // Se o usuário não for admin, só vê os próprios pedidos
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        // Filtrar por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por destino
        if ($request->has('destination')) {
            $query->where('destination', 'LIKE', '%' . $request->destination . '%');
        }

        // Filtrar por intervalo de datas (data de ida)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }

        $trips = $query->get();

        return response()->json($trips, 200);
    }



    public function cancel($id)
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json(['message' => 'Pedido de viagem não encontrado'], 404);
        }

        // Verifica se a viagem já está cancelada
        if ($trip->status === 'cancelado') {
            return response()->json(['message' => 'Esta viagem já foi cancelada'], 400);
        }

        $now = now();
        $tripStartDate = Carbon::parse($trip->departure_date);
        $daysUntilTrip = $tripStartDate->diffInDays($now);

        // Se a viagem já foi aprovada, só um admin pode cancelar, mas apenas com 3 dias de antecedência
        if ($trip->status === 'aprovado') {
            if (!auth()->user()->is_admin) {
                return response()->json(['message' => 'Apenas administradores podem cancelar viagens já aprovadas'], 403);
            }

            if ($daysUntilTrip < 3) {
                return response()->json(['message' => 'Administradores só podem cancelar viagens aprovadas com pelo menos 3 dias de antecedência'], 403);
            }
        }

        // Usuário comum pode cancelar apenas suas próprias viagens, e só se faltar 3 dias ou mais
        if (!auth()->user()->is_admin && $trip->user_id !== auth()->id()) {
            return response()->json(['message' => 'Você não tem permissão para cancelar esta viagem'], 403);
        }

        if (!auth()->user()->is_admin && $daysUntilTrip < 3) {
            return response()->json(['message' => 'Você não pode cancelar a viagem com menos de 3 dias de antecedência'], 403);
        }

        // Cancela a viagem
        $trip->status = 'cancelado';
        $trip->save();

        $user = $trip->user;
        $user->notify(new TripStatusUpdated($trip));

        return response()->json(['message' => 'Viagem cancelada com sucesso'], 200);
    }
}

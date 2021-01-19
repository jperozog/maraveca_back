<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\ticket_problems;

class TicketProblemsController extends Controller
{
    public function index($id)
    {
        $result = DB::table('ticket_problems')
        ->where('ticket_pb', '=', $id)
        ->get();

        return response()->json($result);

    }
    public function store(Request $request)
    {
        return ticket_problems::create($request->all());
    }
    public function delete(Request $request, $id)
    {
        $ticketp = ticket_problems::where('id_pb', '=', $id);
        $ticketp->delete();

        return 204;
    }
}

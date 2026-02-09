<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestOrderController extends Controller
{
    public function show(string $orderNumber, Request $request): View
    {
        $token = $request->query('token');

        if (! $token) {
            abort(404);
        }

        $order = Order::where('order_number', $orderNumber)
            ->where('guest_token', $token)
            ->with(['items', 'statusHistory' => fn ($q) => $q->orderBy('created_at')])
            ->firstOrFail();

        return view('pages.order.status', ['order' => $order]);
    }
}

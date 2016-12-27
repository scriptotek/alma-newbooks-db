<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = \Auth::user();

        $sent = \App\Document::whereIn('po_creator', $user->alma_ids)
            ->whereNull(\App\Document::RECEIVING_OR_ACTIVATION_DATE)
            ->orderBy('sent_date', 'desc')
            ->paginate(100);

        $received = \App\Document::whereIn('po_creator', $user->alma_ids)
            ->whereNotNull(\App\Document::RECEIVING_OR_ACTIVATION_DATE)
            ->orderBy(\App\Document::RECEIVING_OR_ACTIVATION_DATE, 'desc')
            ->paginate(100);

        return view('my-orders', [
            'received' => $received,
            'sent' => $sent,
        ]);
    }
}

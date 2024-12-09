<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_entry;

class POSController extends Controller
{
    public function index(){
        $items = Item_entry::orderBy('item_name', 'asc')->get();

        return view('pos.index',compact('items'));
    }
}

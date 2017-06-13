<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBroadcastRequest;
use App\Http\Requests\DropBroadcastRequest;
use App\Http\Requests\UpdateBroadcastRequest;
use App\Models\BroadcastMessage;
use Illuminate\Contracts\Auth\Guard;

class BroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin', ['only' => [ 'show', 'store', 'update', 'destroy' ]]);
    }

    public function show()
    {
        if ($broadcast = BroadcastMessage::active()) {
            return response()->json([
                'exists' => true,
                'title' => $broadcast->title,
                'content' => $broadcast->content,
            ]);
        }
        return response()->json([ 'exists' => false ]);
    }

    public function store(CreateBroadcastRequest $request)
    {
        if ($active = BroadcastMessage::active()) {
            $active->deactivate();
        }
        BroadcastMessage::create($request->all());
        return response()->json([]);
    }

    public function update(UpdateBroadcastRequest $request)
    {
        BroadcastMessage::active()->update($request->all());
        return response()->json([]);
    }

    public function destroy(DropBroadcastRequest $request)
    {
        BroadcastMessage::active()->deactivate();
        return response()->json([]);
    }

    public function active()
    {
        $message = BroadcastMessage::active();
        if (!$message || $message->isDismissed()) {
            return response()->json(['broadcast' => null]);
        }

        return response()->json(['broadcast' => [
            'title' => $message->title,
            'content' => $message->content
        ]]);
    }
    
    public function dismiss()
    {
        $message = BroadcastMessage::active();
        if (!$message || $message->isDismissed()) {
            return response()->json([]);
        }

        $message->dismiss();
        return response()->json([]);
    }
}

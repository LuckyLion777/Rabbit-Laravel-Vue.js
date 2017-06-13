<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Event;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\GetEventsRequest;
use Validator;
use App\Models\Device;
use App\Jobs\CreateEventJob;
use App\Jobs\GetEventsJob;
use App\Jobs\UpdateEventJob;
use App\Repositories\EventRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;

class ApiController extends Controller
{
	/**
     * @var Dispatcher
     */
    
    private $dispatcher;
    /**
     * ApiController constructor.
     * @param Dispatcher $dispatcher
    */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function listTasks(GetEventsRequest $request)
    {
    	// $device_id
    	$validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,device_id'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'failed','errors' => $validator->errors()]);
        }
        $device = Device::where('device_id', $request->device_id)->first();
        // $credentials = ['email' => $device->user->email, 'password' => $device->user->password];
        if (!Auth::loginUsingId($device->user->id)) {
        	return response()->json(['status' => 'failed','errors' => "Invalid Attempt!"]);
        }
        $objects = $this->dispatcher->dispatchFrom(GetEventsJob::class, $request, ['pureRequest' => $request]);

        return response()->json($objects);
        // $device = Device::where('device_id')->user()->task()
    }

    public function updateEvent(Request $request)
    {
        // |date_format:' . $request->user()->getDateFormatPHP()
        $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'name' => 'required',
            'icon' => 'required',
            'date' => 'required',
            'type_id' => 'required_unless:type,general'
        ],['type_id.required_unless' => 'You must attach event to ' . $request->type]);

        // dd($request);
        $device = Device::where('device_id', $request->device_id)->first();
        // $credentials = ['email' => $device->user->email, 'password' => $device->user->password];
        if (!Auth::loginUsingId($device->user->id)) {
            return response()->json(['status' => 'failed','errors' => "Invalid Attempt!"]);
        }
        
        if ($validator->fails()) {
            return response()->json(['status' => 'failed','errors' => $validator->errors()]);
        } else {
            $event = Event::find($request->task_id);
            $request['event'] = $event;

            return $this->dispatcher->dispatchFrom(UpdateEventJob::class, $request);
        }
    }

}

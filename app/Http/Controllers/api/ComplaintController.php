<?php

namespace App\Http\Controllers\api;
use App\Models\Complaint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Traits\GeneralTrait;

class ComplaintController extends Controller
{
    use GeneralTrait;

    public function store(Request $request)
    {
        try
        {
            $request->validate([
                                    'to_user' => ['required', Rule::exists('users', 'id')],
                                    'complaint' => ['required', 'string'],
                                ]);
            $user = Complaint::find(auth()->user()->id);
            Complaint::create([
                                'from' => auth()->user()->id,
                                'to' => $request->to_user,
                                'complaint' => $request->complaint,
                        ]);

            return $this->returnData('data',__('dashboard.recored created successfully.'),__('dashboard.recored created successfully.'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}

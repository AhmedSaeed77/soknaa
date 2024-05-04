<?php

namespace App\Http\Controllers\dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Http\Mail\SendMail;
use App\Traits\GeneralTrait;

class MailController extends Controller
{
    use GeneralTrait;

    public function sendMail(Request $request)
    {
        $request->validate([
                                'name' => 'required',
                                'email' => 'required|email',
                                'phone' => 'required',
                                'message' => 'required',
                            ]

                        );
        try
        {
            $data = $request->input();
            Mail::to('info@soknaa.com')->send(new SendMail($data));
            return $this->returnData('data',__('site.Email_Send'), __('site.Email_Send'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}

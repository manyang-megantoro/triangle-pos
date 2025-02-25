<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Http\Requests\StoreSettingsRequest;
use Modules\Setting\Http\Requests\StoreSmtpSettingsRequest;
use Modules\Upload\Entities\Upload;

class SettingController extends Controller
{

    public function index() {
        abort_if(Gate::denies('access_settings'), 403);

        $settings = Setting::firstOrFail();

        return view('setting::index', compact('settings'));
    }


    public function update(StoreSettingsRequest $request) {
        $siteLogo = null;
        $logo_url = '';
        if ($request->has('siteLogo') && !is_null($request->siteLogo)) {
            $tempFile = Upload::where('folder', $request->siteLogo)->first();
            //copy file
            Storage::copy('temp/' . $request->siteLogo . '/' . $tempFile->filename, 'public/' . $request->siteLogo . '/' . $tempFile->filename);
            $logo_url = Storage::url($request->siteLogo . '/' . $tempFile->filename);
            Storage::deleteDirectory('temp/' . $request->siteLogo);
            $tempFile->delete();
        }
        
        $data_setting = [
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'company_phone' => $request->company_phone,
            'notification_email' => $request->notification_email,
            'company_address' => $request->company_address,
            'default_currency_id' => $request->default_currency_id,
            'default_currency_position' => $request->default_currency_position,
            'footer_text' => $request->footer_text,
        ];
        
        if($logo_url != ''){
            $data_setting['site_logo'] = $logo_url;
        }
        
        $settings = tap(Setting::firstOrFail())->update($data_setting);


        // if ($request->has('image')) {
        //     $tempFile = Upload::where('folder', $request->image)->first();

        //     if ($tempFile) {
        //         $user->addMedia(Storage::path('public/temp/' . $request->image . '/' . $tempFile->filename))->toMediaCollection('avatars');

        //         Storage::deleteDirectory('public/temp/' . $request->image);
        //         $tempFile->delete();
        //     }
        // }

        cache()->forget('settings');

        toast('Settings Updated!', 'info');

        return redirect()->route('settings.index');
    }


    public function updateSmtp(StoreSmtpSettingsRequest $request) {
        $toReplace = array(
            'MAIL_MAILER='.env('MAIL_HOST'),
            'MAIL_HOST="'.env('MAIL_HOST').'"',
            'MAIL_PORT='.env('MAIL_PORT'),
            'MAIL_FROM_ADDRESS="'.env('MAIL_FROM_ADDRESS').'"',
            'MAIL_FROM_NAME="'.env('MAIL_FROM_NAME').'"',
            'MAIL_USERNAME="'.env('MAIL_USERNAME').'"',
            'MAIL_PASSWORD="'.env('MAIL_PASSWORD').'"',
            'MAIL_ENCRYPTION="'.env('MAIL_ENCRYPTION').'"'
        );

        $replaceWith = array(
            'MAIL_MAILER='.$request->mail_mailer,
            'MAIL_HOST="'.$request->mail_host.'"',
            'MAIL_PORT='.$request->mail_port,
            'MAIL_FROM_ADDRESS="'.$request->mail_from_address.'"',
            'MAIL_FROM_NAME="'.$request->mail_from_name.'"',
            'MAIL_USERNAME="'.$request->mail_username.'"',
            'MAIL_PASSWORD="'.$request->mail_password.'"',
            'MAIL_ENCRYPTION="'.$request->mail_encryption.'"');

        try {
            file_put_contents(base_path('.env'), str_replace($toReplace, $replaceWith, file_get_contents(base_path('.env'))));
            Artisan::call('cache:clear');

            toast('Mail Settings Updated!', 'info');
        } catch (\Exception $exception) {
            Log::error($exception);
            session()->flash('settings_smtp_message', 'Something Went Wrong!');
        }

        return redirect()->route('settings.index');
    }
}

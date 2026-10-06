<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = User::find(session('admin_user_id'));

        $siteSettings = [
            'whatsapp_number'  => PageSection::getSetting('whatsapp_number', config('sazara.whatsapp_display', '+62 812-6040-7208')),
            'whatsapp_clean'   => PageSection::getCleanWhatsApp(),
            'whatsapp_message' => PageSection::getSetting('whatsapp_message', config('sazara.whatsapp_message', 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.')),
            'contact_person'   => PageSection::getSetting('contact_person', config('sazara.contact_person', 'Afriansyah Munar')),
            'email'            => PageSection::getSetting('email', config('sazara.email', 'contact@sazaraglobal.com')),
            'phone'            => PageSection::getSetting('phone', config('sazara.phone', '+62 812-6040-7208')),
            'address'          => PageSection::getSetting('address', config('sazara.address', 'Medan, North Sumatra, Indonesia')),
        ];

        return view('admin.settings.index', compact('user', 'siteSettings'));
    }

    public function updateSite(Request $request)
    {
        $request->validate([
            'whatsapp_number'  => ['required', 'string', 'max:50'],
            'whatsapp_message' => ['nullable', 'string', 'max:1000'],
            'contact_person'   => ['nullable', 'string', 'max:100'],
            'email'            => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'address'          => ['nullable', 'string', 'max:500'],
        ]);

        // Save site-wide settings
        PageSection::set('settings', 'general', 'whatsapp_number', trim($request->whatsapp_number));
        PageSection::set('settings', 'general', 'whatsapp_message', trim($request->whatsapp_message ?? ''));

        if ($request->filled('contact_person')) {
            PageSection::set('settings', 'general', 'contact_person', trim($request->contact_person));
        }

        if ($request->filled('email')) {
            PageSection::set('settings', 'general', 'email', trim($request->email));
            PageSection::set('contact', 'info', 'email', trim($request->email));
        }

        if ($request->filled('phone')) {
            PageSection::set('settings', 'general', 'phone', trim($request->phone));
        }

        if ($request->filled('address')) {
            PageSection::set('settings', 'general', 'address', trim($request->address));
            PageSection::set('contact', 'info', 'address', trim($request->address));
        }

        return back()->with('success', 'Global site settings updated successfully.');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(session('admin_user_id'));

        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email,' . $user->id],
            'current_password'      => ['nullable', 'string'],
            'new_password'          => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // If changing password, verify current
        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        session(['admin_name' => $user->name]);

        return back()->with('success', 'Admin account settings updated.');
    }
}

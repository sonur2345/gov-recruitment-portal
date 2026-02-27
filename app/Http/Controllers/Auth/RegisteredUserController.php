<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'category' => ['required', 'in:GEN,OBC,SC,ST,EWS'],
            'marital_status' => ['required', 'in:single,married,widowed'],
            'nationality' => ['required', 'string', 'max:60'],
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'correspondence_address' => ['required', 'string', 'max:2000'],
            'permanent_address' => ['required', 'string', 'max:2000'],
            'aadhaar_number' => ['nullable', 'regex:/^[0-9]{12}$/'],
            'aadhar_number' => ['nullable', 'regex:/^[0-9]{12}$/'],
            'id_proof_upload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'declaration' => ['required', 'accepted'],
        ]);

        $aadhaarNumber = $validated['aadhaar_number'] ?? $validated['aadhar_number'] ?? null;
        $idProofPath = $request->file('id_proof_upload')->store('users/id-proofs', 'local');

        $user = User::create([
            'name' => $validated['name'],
            'father_name' => $validated['father_name'],
            'mother_name' => $validated['mother_name'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'category' => $validated['category'],
            'marital_status' => $validated['marital_status'],
            'nationality' => $validated['nationality'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'correspondence_address' => $validated['correspondence_address'],
            'permanent_address' => $validated['permanent_address'],
            'aadhaar_number' => $aadhaarNumber,
            'id_proof_path' => $idProofPath,
            'password' => Hash::make($validated['password']),
        ]);

        if (Role::query()->where('name', 'Candidate')->exists()) {
            $user->assignRole('Candidate');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route($user->dashboardRouteName(), absolute: false));
    }
}

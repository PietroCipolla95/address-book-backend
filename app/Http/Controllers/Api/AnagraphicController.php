<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anagraphic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AnagraphicController extends Controller
{
    public function index()
    {
        // fetch all anagraphics with contacts
        $anagraphics = Anagraphic::get();
        return response()->json([
            'success' => true,
            'result' => $anagraphics
        ]);
    }

    public function show(Anagraphic $anagraphic)
    {

        // show an anagraphic with corresponding contacts
        return response()->json([
            'success' => true,
            'result' => $anagraphic
        ]);
    }

    public function store(Request $request)
    {
        // validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg|max:32768',
            'note' => 'nullable|string|max:255',
        ]);

        // process and store the image
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $binaryImage = file_get_contents($file->path());
            $validatedData['photo'] = $binaryImage;
        } else {
            // use a default image:
            $validatedData['photo'] = file_get_contents(Storage::path('thumbnails/contact.png'));
        }

        // Save the Anagraphic with the image data
        $anagraphic = Anagraphic::create($validatedData);

        return response()->json([
            'success' => true,
            'result' => $anagraphic,
        ]);
    }
    public function update(Request $request, Anagraphic $anagraphic)
    {
        // validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg|max:32768',
            'notes' => 'nullable|string|max:255',
        ]);

        // update anagraphic
        $anagraphic->update($validatedData);

        // sync contacts if provided
        if ($request->has('contacts')) {
            $anagraphic->contact()->sync($request->input('contacts'));
        }

        // check if request has photo
        if ($request->has('photo')) {

            // store the new photo
            $path = Storage::put('thumbnails', $request->photo);
            $validatedData['photo'] = $path;

            // if there's an existing photo delete it
            if (!is_Null($anagraphic->photo) && Storage::fileExists($anagraphic->photo)) {
                Storage::delete($anagraphic->photo);
            }
        }

        return response()->json([
            'success' => true,
            'result' => $anagraphic,
            'message' => 'Anagraphic updated successfully',
        ]);
    }

    public function destroy(Anagraphic $anagraphic)
    {

        $anagraphic->update(['deleted' => !$anagraphic->deleted]);

        $anagraphic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anagraphic soft deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $anagraphics = Anagraphic::where('name', 'LIKE', $keyword . '%')->get();

        return response()->json([
            'success' => true,
            'result' => $anagraphics,
        ]);
    }


    public function getContacts(Anagraphic $anagraphic)
    {
        $contacts = $anagraphic->contacts;

        return response()->json([
            'success' => true,
            'contacts' => $contacts,
        ]);
    }

    public function addContact(Request $request, Anagraphic $anagraphic)
    {

        $validatedData = $request->validate([
            'type' => 'required',
            'contact' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') !== 'email') {
                        // Se il tipo è diverso da email, verifica il formato E.164
                        if (!preg_match('^\+?\d{6,7}[2-9]\d{3}$^', $value)) {
                            $fail('phone number formatting not valid');
                        }
                    } elseif ($request->input('type') === 'email') {
                        // Se il tipo è email, verifica che sia un indirizzo email valido
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('email not valid');
                        }
                    }
                },
            ],
            'notes' => 'nullable|string|max:255',
        ]);

        $validatedData['type'] = $request->type;

        $contact = $anagraphic->contacts()->create($validatedData);

        return response()->json([
            'success' => true,
            'contact' => $contact,
            'message' => 'Contact added successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anagraphic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AnagraphicController extends Controller
{
    public function index()
    {
        // fetch all anagraphics with contacts
        $anagraphics = Anagraphic::all();
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
        if ($request->has('photo')) {

            // save image with path
            $path = Storage::put('thumbnails', $request->photo);
            $validatedData['photo'] = $path;
        } else {
            // if no image is provided, use the default thumbnail
            $validatedData['photo'] = 'thumbnails/contact.png';
        }

        // Save the Anagraphic with the image path
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
        $anagraphic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anagraphic soft deleted successfully',
        ]);
    }

    public function search($string)
    {
        $anagraphics = Anagraphic::with('contact')
            ->where('name', 'LIKE', '%' . $string . '%')
            ->get();

        return response()->json([
            'success' => true,
            'result' => $anagraphics,
        ]);
    }


    public function getContacts($id)
    {
        $anagraphic = Anagraphic::with('contact')->find($id);

        if (!$anagraphic) {
            return response()->json([
                'success' => false,
                'result' => 'Anagraphic not found',
            ]);
        }

        return response()->json([
            'success' => true,
            'result' => $anagraphic->contact,
        ]);
    }

    public function addContact(Request $request, $id)
    {
        // find the anagraphic by ID
        $anagraphic = Anagraphic::findOrFail($id);

        // attach contacts
        $anagraphic->contact()->attach($request->input('contacts'));

        return response()->json([
            'success' => true,
            'result' => $anagraphic->contact,
            'message' => 'Contacts added successfully',
        ]);
    }
}

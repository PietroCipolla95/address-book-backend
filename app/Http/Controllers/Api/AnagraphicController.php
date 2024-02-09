<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anagraphic;
use Illuminate\Http\Request;

class AnagraphicController extends Controller
{
    public function index()
    {
        // fetch all anagraphics with contacts
        $anagraphics = Anagraphic::all()->where('deleted', 0);
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
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg',
            'notes' => 'nullable|string|max:255',
        ]);

        $anagraphic = new Anagraphic();
        $anagraphic->name = $request->name;
        $anagraphic->notes = $request->notes;

        if ($request->hasFile('photo')) {
            //if file is provided

            $image = $request->photo;

            // Resize the image to 256x256
            list($width, $height, $type) = getimagesize($image);
            $newWidth = 256;
            $newHeight = 256;
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($image);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($image);
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                    break;
            }

            // Resize and save as PNG
            imagecopyresized($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            ob_start();
            imagepng($resizedImage);

            $resizedImageBase64 = base64_encode(ob_get_clean());

            $anagraphic->photo = $resizedImageBase64;

            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            $anagraphic->save();
        } else {
            // if no file is provided
            $defaultImage = public_path('/storage/thumbnails/contact.png');

            list($width, $height, $type) = getimagesize($defaultImage);
            $newWidth = 256;
            $newHeight = 256;
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            switch ($type) {
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($defaultImage);
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                    break;
            }

            imagecopyresized($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            ob_start();
            imagepng($resizedImage);

            $resizedImageBase64 = base64_encode(ob_get_clean());

            $anagraphic->photo = $resizedImageBase64;
            $anagraphic->save();
        }

        $anagraphic->save();

        return response()->json([
            'success' => true,
            'message' => 'Anagraphic added',
        ]);
    }

    public function update(Request $request, Anagraphic $anagraphic)
    {
        // validation
        //error_log(print_r($request, true));

        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg',
            'notes' => 'nullable|string|max:255',
        ]);

        // sync contacts if provided
        if ($request->has('contacts')) {
            $anagraphic->contact()->sync($request->input('contacts'));
        }

        // check if request has photo
        if ($request->hasFile('photo')) {
            //if file is provided

            $image = $request->photo;

            // Resize the image to 256x256
            list($width, $height, $type) = getimagesize($image);
            $newWidth = 256;
            $newHeight = 256;
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($image);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($image);
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                    break;
            }

            // Resize and save as PNG
            imagecopyresized($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            ob_start();
            imagepng($resizedImage);

            $resizedImageBase64 = base64_encode(ob_get_clean());

            $anagraphic->photo = $resizedImageBase64;

            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            $anagraphic->save();
        }

        // update anagraphic
        $anagraphic->update($request->all());


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

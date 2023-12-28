<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    // Show a single contact
    public function show(Contact $contact)
    {
        return response()->json([
            'success' => true,
            'contact' => $contact,
        ]);
    }

    // Update the contact
    public function update(Request $request, Contact $contact)
    {
        $validatedData = $request->validate([
            'contact' => 'required|string|max:255',
            'type' => 'required',
            'notes' => 'nullable|string|max:255',
        ]);

        $contact->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'contact' => $contact,
        ]);
    }

    // Delete the contact
    public function destroy(Contact $contact)
    {
        $contact->update(['deleted' => !$contact->deleted]);

        //$contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully',
        ]);
    }

    public function searchByType($type)
    {
        $contacts = Contact::where('type', 'LIKE', $type . '%')->get();

        return response()->json($contacts);
    }
}

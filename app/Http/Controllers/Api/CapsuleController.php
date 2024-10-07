<?php

namespace App\Http\Controllers\Api;

use App\Models\Capsule;
use App\Models\capsules;
use Illuminate\Http\Request;
use App\Models\received_capsule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CapsuleResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CapsuleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['show'])
        ];
    }   

    public function index(Request $request) {

        $user = $request->user(); // Get the authenticated user

    // Retrieve capsules sent to the user based on the receiver_email
    $capsules = capsules::where('receiver_email', $user->email)->get();

    return response()->json($capsules, 200);
        
    }

    public function destroy(capsules $capsule) {

        // Check if the capsule exists
        Gate::authorize('modify', $capsule);
        
        if (!$capsule) {
            return response()->json(['message' => 'Capsule not found'], 404);
        }
    
            // Delete the specific capsule
            $capsule->delete();
    
            return response()->json(['message' => 'Capsule deleted successfully'], 200);
    }

    public function store(Request $request) {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'title' => 'required|max:50|string',
                'message' => 'required|max:500|string',
                'content' => 'nullable',
                'receiver_email' => 'required|string|email|max:255',
                'opens_at' =>'nullable'
            ]);
    
            // user authentication
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            // Create the capsule with the authenticated user's ID
            $createdCapsule = $request->user()->capsules()->create(array_merge($validatedData, [
                'user_id' => $request->user()->id 
            ]));
    
            return response()->json($createdCapsule, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);

        } 

        catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the capsule.'], 500);
        }
    }
    
    
    public function update(Request $request, capsules $capsule) {

        Gate::authorize('modify', arguments: $capsule);

        // Check if the capsule exists
        if (!$capsule) {
            return response()->json(['message' => 'Capsule not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|max:50|string',
            'message' => 'required|max:500|string'
        ]);

        // Update the capsule with the validated data

        $capsule->update($validatedData);

        return response()->json([
            'message'=> 'Updated Successfully',
            'info' => $capsule
        ], 200);
        }
}

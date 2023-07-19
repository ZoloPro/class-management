<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 400);
        }

        $lecturer = Auth::guard('lecturerToken')->user();
        $classroom = $lecturer->classrooms()->find($request->classroomId);
        if (!$classroom) {
            return response()->json([
                'success' => 0,
                'message' => 'Classroom does not exist',
                'data' => []], 400);
        }

        $fileName = time() . '_' . $request->file->getClientOriginalName();
        // save file to azure blob virtual directory uplaods in your container
        $folder = "{$classroom->term->termName}_{$classroom->id}";
        $path = $request->file('file')->storeAs($folder, $fileName, 'azure');
        $url = Storage::disk('azure')->url($path);
        $uploadedFileName = pathinfo($path, PATHINFO_FILENAME) . '.' . $request->file->getClientOriginalExtension();
        Document::create([
            'classroomId' => $classroom->id,
            'fileName' => $uploadedFileName,
            'url' => $url,
        ]);
        return response()->json([
            'success' => 1,
            'message' => 'File uploaded successfully',
            'data' => [
                'file_path' => $path
            ]
        ]);
    }

    public function getDocumentsByClassroom(Request $request)
    {

        $student = Auth::user();
        $classroom = $student->registeredClassrooms()->find($request->classroomId);
        if (!$classroom) {
            return response()->json([
                'success' => 0,
                'message' => 'Classroom does not exist',
                'data' => []], 400);
        }
        $documents = $classroom->documents()->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => ['documents' => $documents],
        ], 200);
    }
}

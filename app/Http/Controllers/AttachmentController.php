<?php

namespace App\Http\Controllers;

use App\Models\ProjectAttachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function show(ProjectAttachment $attachment)
    {
        // Optional: pastikan user login
        $this->middleware('auth');

        $disk = Storage::disk('public');

        if (! $disk->exists($attachment->path)) {
            abort(404);
        }

        $filename = $attachment->original_name ?: basename($attachment->path);
        $mime     = $attachment->mime_type ?: $disk->mimeType($attachment->path);

        // tampilkan langsung di browser (PDF / gambar)
        return $disk->response($attachment->path, $filename, [
            'Content-Type' => $mime,
        ]);
    }
}

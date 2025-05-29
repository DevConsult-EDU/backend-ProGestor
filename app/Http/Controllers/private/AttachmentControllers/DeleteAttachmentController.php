<?php

namespace App\Http\Controllers\private\AttachmentControllers;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;

class DeleteAttachmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $attachment = Attachment::find($request->route("id"));

        $attachment->delete();
    }
}

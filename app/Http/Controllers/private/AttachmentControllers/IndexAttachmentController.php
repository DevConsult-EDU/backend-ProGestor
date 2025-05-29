<?php

namespace App\Http\Controllers\private\AttachmentControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexAttachmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $attachemnts = DB::table('attachments')->where('task_id', $request->route('id'))->get();

        $ArchivosSubidos = [];
        foreach ($attachemnts as $attachemnt) {
            $ArchivosSubidos[] = [
                'id' => $attachemnt->id,
                'task_id' => $attachemnt->task_id,
                'user_id' => $attachemnt->user_id,
                'file_name' => $attachemnt->file_name,
                'system_name' => $attachemnt->system_name,
                'type_MIME' => $attachemnt->type_MIME,
                'byte_size' => $attachemnt->byte_size,
                'store_path' =>  $attachemnt->store_path,
                'created_at' => $attachemnt->created_at,
            ];
        }

        return response()->json($ArchivosSubidos);
    }
}

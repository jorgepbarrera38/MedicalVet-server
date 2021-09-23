<?php

namespace App\Http\Controllers;

use App\Models\AttachmentType;
use Illuminate\Http\Request;

class AttachmentTypeController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:attachment-types.index')->only('index');
        $this->middleware('has.permission:attachment-types.store')->only('store');
        $this->middleware('has.permission:attachment-types.update')->only('update');
        $this->middleware('has.permission:attachment-types.destroy')->only('destroy');
    }

    public function index()
    {
        return AttachmentType::where('name', 'like', '%' . request()->search . '%')->paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:attachment_types,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        return AttachmentType::create($data);
    }

    public function show(AttachmentType $attachment_type) {
        return $attachment_type;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AttachmentType  $attachmentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttachmentType $attachment_type)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:attachment_types,name,' . $attachment_type->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $attachment_type->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttachmentType  $attachmentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttachmentType $attachment_type)
    {
        if ($attachment_type->clinicHistoryAttachments()->count()) {
            abort(422);
        }
        $attachment_type->delete();
    }
}

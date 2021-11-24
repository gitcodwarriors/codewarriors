<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Storage;

class InvoicesAndAttachment extends Model
{
	protected $appends = ['attachment_view_path', 'attachment_download_path'];

	public function getByFacilityIdAndYear($facilityId, $year)
	{
		return $this->where('facility_id', $facilityId)->where('year', $year)->get()->groupBy('month');
	}

	public function getByFacilityIdAndMonth($facilityId, $year, $month)
	{
		return $this->where('facility_id', $facilityId)->where('year', $year)->where('month', $month)->get()->groupBy('month');
	}

	public function insertAttachments($request)
	{
        $user = Auth::user();
        $month = $request->input('selectedMonth');
        $year = $request->input('selectedYear');
        $facilityId = $user->facilityData->facility_id;
		if($request->total_files > 0) {   
            for ($x = 0; $x < $request->total_files; $x++) {
                //write code here to save the uploaded files as attachment
            }
        }
        return $this->getByFacilityIdAndYear($facilityId, $year);
	}

    public function getAttachmentViewPathAttribute()
    {
        $url = config('attachments.invoices_attachment_folder_path');
        $url = str_replace('{facility_id}', $this->facility_id, $url);
        $url = str_replace('{year}', $this->year, $url);
        $url = str_replace('{month}', $this->month, $url);
        $url = $url . $this->attachment;
        return $url;
    }

    public function getAttachmentDownloadPathAttribute()
    {
        $url = config('attachments.invoices_attachment_folder_path');
        $url = str_replace('{facility_id}', $this->facility_id, $url);
        $url = str_replace('{year}', $this->year, $url);
        $url = str_replace('{month}', $this->month, $url);
        $url = $url . $this->attachment;
        return $url;
    }

    public function deleteById($id)
    {
        $attachment = $this->find($id);
        $attachment_view_path = str_replace('storage/', '', $attachment->attachment_view_path);
        unlink(Storage::disk('public')->path($attachment_view_path));
    	return $this->destroy($id);
    }

    public function findAndUpdateName($data)
    {
        $attachment = $this->find($data['id']);
        $attachment->name = $data['name'] . '.' . $attachment->file_extension;
        $attachment->save();
    }
}
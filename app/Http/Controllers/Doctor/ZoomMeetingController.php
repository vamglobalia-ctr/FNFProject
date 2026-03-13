<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Followups;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomMeetingController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function createMeeting(Request $request, $id)
    {
        try {
            if (str_starts_with($id, 'initial_')) {
                $inquiryId = str_replace('initial_', '', $id);
                $inquiry = \App\Models\PatientInquiry::findOrFail($inquiryId);
                
                $topic = "Consultation with " . $inquiry->patient_name;
                // Use inquiry_time meta if available, otherwise default to 10:00
                $inquiryTime = $inquiry->getMeta('inquiry_time');
                $startTime = $inquiry->inquiry_date->format('Y-m-d') . 'T' . ($inquiryTime ? $inquiryTime : '10:00') . ':00Z';
                
                $meeting = $this->zoomService->createMeeting($topic, $startTime, 30);
                
                if (isset($meeting['id'])) {
                    $inquiry->setMeta('zoom_meeting_id', $meeting['id']);
                    $inquiry->setMeta('zoom_start_url', $meeting['start_url']);
                    $inquiry->setMeta('zoom_join_url', $meeting['join_url']);
                    $inquiry->setMeta('zoom_password', $meeting['password'] ?? null);
                    
                    return back()->with('success', 'Zoom meeting created successfully for initial inquiry.');
                }
                
                $errorMessage = $meeting['error'] ?? 'Failed to create Zoom meeting.';
                return back()->with('error', $errorMessage);
            }

            $followup = Followups::findOrFail($id);

            // Fetch patient name for meeting topic
            $patientName = $followup->inquiry ? $followup->inquiry->patient_name : 'Patient';
            $topic = "Consultation with " . $patientName;
            
            // Format: YYYY-MM-DDTHH:MM:SSZ
            $dateStr = is_string($followup->followup_date) ? $followup->followup_date : $followup->followup_date->format('Y-m-d');
            $startTime = $dateStr . 'T10:00:00Z'; 
            
            if ($followup->metas) {
                $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
                if ($timeMeta) {
                    $startTime = $dateStr . 'T' . $timeMeta->meta_value . 'Z';
                }
            }

            $meeting = $this->zoomService->createMeeting($topic, $startTime, 30);

            if (isset($meeting['id'])) {
                $followup->update([
                    'zoom_meeting_id' => $meeting['id'],
                    'zoom_start_url' => $meeting['start_url'],
                    'zoom_join_url' => $meeting['join_url'],
                    'zoom_password' => $meeting['password'] ?? null,
                ]);

                return back()->with('success', 'Zoom meeting created successfully.');
            }

            $errorMessage = $meeting['error'] ?? 'Failed to create Zoom meeting.';
            return back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('Zoom Meeting Creation Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function joinMeeting($id)
    {
        try {
            $meetingId = null;
            $zoomJoinUrl = null;
            $zoomPassword = null;
            $displayName = 'Guest';

            if (str_starts_with($id, 'initial_')) {
                $inquiryId = str_replace('initial_', '', $id);
                $inquiry = \App\Models\PatientInquiry::findOrFail($inquiryId);
                
                $meetingId = $inquiry->getMeta('zoom_meeting_id');
                $zoomJoinUrl = $inquiry->getMeta('zoom_join_url');
                $zoomPassword = $inquiry->getMeta('zoom_password');
                $displayName = $inquiry->patient_name ?? 'Patient';
                
            } else {
                $followup = Followups::findOrFail($id);
                $meetingId = $followup->zoom_meeting_id;
                $zoomJoinUrl = $followup->zoom_join_url;
                $zoomPassword = $followup->zoom_password;
                $displayName = $followup->inquiry ? $followup->inquiry->patient_name : 'Patient';
            }
            
            if (!$meetingId) {
                return back()->with('error', 'Zoom meeting not found.');
            }

            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('Superadmin')) {
                    $displayName = 'Admin: ' . $user->name;
                } elseif ($user->user_role == 6) { // Doctor
                    $displayName = 'Dr. ' . $user->name;
                }
            }

            $passwordHash = '';
            
            if ($zoomJoinUrl) {
                parse_str(parse_url($zoomJoinUrl, PHP_URL_QUERY), $query);
                $passwordHash = $query['pwd'] ?? '';
            }

            // Hashed password token for auto-bypass of passcode screen
            $pwdParam = $passwordHash ?: $zoomPassword;

            // Direct Web Client Join Link
            $zoomUrl = "https://app.zoom.us/wc/{$meetingId}/join?pwd={$pwdParam}";

            return redirect($zoomUrl);

        } catch (\Exception $e) {
            Log::error('Zoom Join Error: ' . $e->getMessage());
            return back()->with('error', 'Error joining Zoom: ' . $e->getMessage());
        }
    }
}

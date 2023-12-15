<?php

namespace App\Http\Controllers\API\UserManagement;

use App\Events\UserCVUploadedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadCVRequest;
use App\Http\Response;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    /**
     * Uploads a CV file for the authenticated user.
     *
     * @param UploadCVRequest $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function uploadCV(UploadCVRequest $request): JsonResponse
    {
        $user = auth()->user();
        $profile = $user->profile;

        try {
            $this->authorize('uploadCV', $profile);

            $file = $request->file('cv_file');
            $filePath = $file->storePublicly('cv_files', 'public');

            event(new UserCVUploadedEvent($user, $filePath));

            return Response::send(true, 200, 'CV file has been uploaded and is processing');
        } catch (\Exception $e) {
            return Response::send(false, 500, $e->getMessage());
        }
    }
}

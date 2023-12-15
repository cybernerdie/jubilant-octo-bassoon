<?php

namespace App\Listeners;

use App\Actions\UpdateUserProfileAction;
use App\Events\UserCVUploadedEvent;
use App\Models\User;
use Spatie\PdfToText\Pdf;

class ProcessUserCVUploadListener
{
    /**
     * Handle the UserCVUploadedEvent.
     *
     * @param UserCVUploadedEvent $event
     * @return void
     */
    public function handle(UserCVUploadedEvent $event): void
    {
        $binPath = config('pdf.pdf_to_text_bin_path');
        $user = $event->user;
        $filePath = "storage/{$event->filePath}";
        $text = $this->extractTextFromPDF($binPath, $filePath);

        $technicalSkills = $this->extractTechnicalSkills($text);
        $workExperience = $this->extractWorkExperience($text);
        $education = $this->extractEducation($text);

        $userData = [
            'skills' => $technicalSkills,
            'job_experience' => $workExperience,
            'educational_background' => $education,
        ];

        $this->updateUserProfile($user, $userData);
        $this->deleteFile($filePath);
    }

    /**
     * Extract text from a PDF file.
     *
     * @param string $binPath
     * @param string $filePath
     * @return string
     */
    private function extractTextFromPDF(string $binPath, string $filePath): string
    {
        return (new Pdf($binPath))->setPdf($filePath)->text();
    }

    /**
     * Extract technical skills from the extracted text.
     *
     * @param string $text
     * @return array
     */
    private function extractTechnicalSkills(string $text): array
    {
        if (preg_match('/(?:SKILLS|TECHNICAL SKILLS)(.*?)(?=(WORK EXPERIENCE|EXPERIENCE|EDUCATION))/s', $text, $matches)) {
            $technicalSkills = $matches[1];
            return array_filter(explode(PHP_EOL, trim($technicalSkills)));
        }

        return [];
    }

    /**
     * Extract work experience from the extracted text.
     *
     * @param string $text
     * @return string
     */
    private function extractWorkExperience(string $text): string
    {
        if (preg_match('/(?:WORK EXPERIENCE|EXPERIENCE)(.*?)(?=(EDUCATION|EDUCATIONAL BACKGROUND))/s', $text, $matches)) {
            return json_encode($matches[1]);
        }

        return [];
    }

    /**
     * Extract education from the extracted text.
     *
     * @param string $text
     * @return string
     */
    private function extractEducation(string $text): string
    {
        if (preg_match('/(?:EDUCATION|EDUCATIONAL BACKGROUND)(.*?)(?=(PERSONAL PROJECTS|$))/s', $text, $matches)) {
            return json_encode($matches[1]);
        }

        return [];
    }

    /**
     * Update user profile with the extracted data.
     *
     * @param User $user
     * @param array $userData
     * @return void
     */
    private function updateUserProfile(User $user, array $userData): void
    {
        $updateProfileAction = resolve(UpdateUserProfileAction::class);
        $updateProfileAction->execute($user, $userData);
    }

    /**
     * Delete the uploaded file.
     *
     * @param string $filePath
     * @return void
     */
    private function deleteFile(string $filePath): void
    {
        unlink(public_path($filePath));
    }
}

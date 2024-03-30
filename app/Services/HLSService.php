<?php
namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class HLSService 
{
    
    // public function retrieveVideo(Request $request)
    // {
    //     // Validate and store the uploaded video
        
    //     // Process the uploaded video
    //     $segmentsDirectory = $this->hlsFormat($videoPath);
    //     // Return the URL to the manifest file
    //     $manifestUrl = Storage::url($segmentsDirectory . '/playlist.m3u8');
    //     return response()->json(['manifest_url' => $manifestUrl]);
    // }


    public function hlsFormat($videoPath){
        // Extract the filename without the extension
        $fileName = pathinfo($videoPath, PATHINFO_FILENAME);
        // Define output directory for segments
        $hlsFormatDirectory = 'public/hls/' . $fileName;
        // Ensure the segments directory exists
        Storage::makeDirectory($hlsFormatDirectory);
        // Run FFmpeg command to split the video into segments
        $process = new Process([
            'ffmpeg',
            '-i', storage_path('app/' . $videoPath),
            '-c:v', 'copy',
            '-c:a', 'copy',
            '-map', '0',
            '-f', 'segment',
            '-segment_time', '10', // 10 seconds per segment
            '-segment_list', storage_path('app/' . $hlsFormatDirectory . '/' . $fileName . '_playlist.m3u8'),
            storage_path('app/' . $hlsFormatDirectory . '/' . $fileName . '_output_segment_%03d.ts')
        ]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Failed to split video into segments');
        }
        // Store the manifest filename in the database
        $manifestFileName = $fileName . '_playlist.m3u8';
        $HlsData = [$hlsFormatDirectory, $manifestFileName];
        return $HlsData;
    }





}
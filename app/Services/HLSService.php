<?php
namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class HLSService 
{
    public function hlsFormat($videoPath){
        // Extract the filename without the extension
        $fileName = pathinfo($videoPath, PATHINFO_FILENAME);
    
        $ownerId = null;
        // Use regular expression to extract the number inside parentheses
        $pattern = '/(\d+)$/';
        if (preg_match($pattern, $fileName, $matches)) {
            $ownerId = $matches[1];
            echo "Owner ID: " . $ownerId;
        } else {
            echo "Owner ID not found.";
        }
    
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
            '-segment_time', '2', // 10 seconds per segment
            '-segment_list', storage_path('app/' . $hlsFormatDirectory . '/' . $fileName . '_playlist.m3u8'),
            storage_path('app/' . $hlsFormatDirectory . '/' . $fileName . '_output_segment_%03d.ts')
        ]);
        $process->run();
    
        // Check if the FFmpeg process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        // Store the manifest filename in the database
        $manifestFileName = $fileName . '_playlist.m3u8';
        $HlsData = ['hlsFormatDirectory'=>$hlsFormatDirectory, 'manifestFileName'=>$manifestFileName, 'ownerId'=>$ownerId];
        return $HlsData;
    }

}
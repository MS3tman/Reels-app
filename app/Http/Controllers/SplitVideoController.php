<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class SplitVideoController extends Controller
{

    // public function start(){
    //     $videoPath = 'app/public/video';
    //     $outputDirectory = 'app/public/store';
    //     $chunkDuration = 5;
    //     $this->splitVideoIntoChunks($videoPath, $outputDirectory, $chunkDuration);
    //     return response()->json(['message'=>'good'], 200);
    // }


    // public function splitVideoIntoChunks($videoPath, $outputDirectory, $chunkDuration)
    // {
    //     // Create an instance of FFMpeg
    //     $ffmpeg = FFMpeg::create();

    //     // Open the video file
    //     $video = $ffmpeg->open($videoPath);

    //     // Get the duration of the original video
    //     $duration = $video->getDurationInSeconds();

    //     // Calculate the number of chunks based on the chunk duration
    //     $numChunks = ceil($duration / $chunkDuration);

    //     // Split the video into chunks
    //     for ($i = 0; $i < $numChunks; $i++) {
    //         $startTime = $i * $chunkDuration;
    //         $endTime = min(($i + 1) * $chunkDuration, $duration);

    //         // Set the output file name for the chunk
    //         $outputFileName = "chunk_$i.mp4";
    //         $outputFilePath = "$outputDirectory/$outputFileName";

    //         // Export the chunk
    //         $video
    //             ->clip(\FFMpeg\Coordinate\TimeCode::fromSeconds($startTime), \FFMpeg\Coordinate\TimeCode::fromSeconds($endTime))
    //             ->export()
    //             ->toDisk('local')
    //             ->inFormat(new \FFMpeg\Format\Video\X264())
    //             ->save($outputFilePath);
    //     }

    //     return $numChunks;
    // }
}

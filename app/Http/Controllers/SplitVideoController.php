<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use FFMpeg;
use FFMpeg\FFMpeg;
use FFMpeg\FFMpeg as FFMpegLib;
use FFMpeg\Coordinate\TimeCode;


class SplitVideoController extends Controller
{
    // protected $ffmpeg;

    // public function __construct()
    // {
    //     $this->ffmpeg = FFMpeg::create();
    // }

    
    // public function splitVideoIntoChunks($videoPath, $chunkSizeInSeconds)
    // {
    //     $ffmpeg = $this->ffmpeg;

    //     // Open the video file
    //     $video = $ffmpeg->open($videoPath);

    //     // Get the duration of the video in seconds
    //     $duration = $video->getDuration();

    //     // Calculate the total number of chunks based on the chunk size
    //     $totalChunks = ceil($duration / $chunkSizeInSeconds);

    //     $chunks = [];

        // // Split the video into chunks
        // for ($i = 0; $i < $totalChunks; $i++) {
        //     // Calculate start time for the current chunk
        //     $startTime = $i * $chunkSizeInSeconds;

        //     // Calculate end time for the current chunk
        //     $endTime = min(($i + 1) * $chunkSizeInSeconds, $duration);
        //     // Define the output path for the current chunk
        //     $outputPath = storage_path('app/chunks/') . "chunk_$i.mp4";

        //     // Extract the chunk from the video
        //     $video->filters()
        //         ->clip(TimeCode::fromSeconds($startTime), TimeCode::fromSeconds($endTime))
        //         ->export()
        //         ->toDisk('local')
        //         ->inFormat(new \FFMpeg\Format\Video\X264)
        //         ->save($outputPath);

        //     // Add information about the chunk to the array
        //     $chunks[] = [
        //         'index' => $i,
    //             'path' => $outputPath
    //         ];
    //     }
    //     return [
    //         'totalChunks' => $totalChunks,
    //         'chunks' => $chunks
    //     ];
    // }
}

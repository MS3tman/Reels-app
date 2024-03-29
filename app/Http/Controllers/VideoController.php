<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class VideoController extends Controller
{
    public function uploadChunk(Request $request){
        $validator = Validator::make($request->all(), [
            'filename'=>'required',
            'total_chunks'=>'required',
            'chunk'=>'required',
            'chunk_index'=>'request'
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()]);
        }
        // Validate incoming request and retrieve necessary data
        if ($request->hasFile('chunk')) {
            $chunk = $request->file('chunk');
            $chunkIndex = $request->input('chunk_index');
            $totalChunks = $request->input('total_chunks');
            $filename = $request->input('filename');
            // Store the chunk in a temporary folder
            $chunkTempFolder = 'public/tempChunks';
            $chunk->storeAs($chunkTempFolder, $filename . '-' . $chunkIndex);
            // Check if all chunks are received and assembled
            if ($chunkIndex === $totalChunks - 1) {
                // Assemble the video file from chunks
                $this->assembleVideo($filename, $chunkTempFolder, $totalChunks);
                // Clean up the temporary folder
                $this->cleanUpTemporaryFolder($chunkTempFolder);
                return response()->json(['message' => 'Video uploaded successfully!']);
            }
            // Return success for current chunk, client sends the next chunk
            return response()->json(['message' => 'Chunk uploaded successfully']);
        } else {
            // Handle missing chunk or error
            return response()->json(['error' => 'Missing video chunk'], 400);
        }
    }


    public function assembleVideo(string $filename, string $chunkTempFolder, int $totalChunks){
        // Logic to concatenate all chunks in the temporary folder
        // into a single file named $filename in the desired location
        $assembledVideo = '';
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = storage_path("app/{$chunkTempFolder}/{$filename}-{$i}");
            $assembledVideo .= file_get_contents($chunkPath);
        }
        // Save the assembled video file
        $videoTempFolder = 'public/tempVideos';
        $assembledVideoPath = storage_path("app/{$videoTempFolder}/{$filename}");
        file_put_contents($assembledVideoPath, $assembledVideo);
        //return $videoTempFolder . '/' . $filename;
    }


    public function cleanUpTemporaryFolder(string $chunkTempFolder){
        // Delete all files in the temporary folder
        $tempFolderPath = storage_path("app/{$chunkTempFolder}");
        $files = scandir($tempFolderPath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink("{$tempFolderPath}/{$file}");
            }
        }
    }


//     <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Symfony\Component\Process\Process;

// class VideoController extends Controller
// {
//     public function uploadVideo(Request $request)
//     {
//         // Validate and store the uploaded video
//         $videoPath = $request->file('video')->store('videos');

//         // Process the uploaded video
//         $segmentsDirectory = $this->splitVideoIntoSegments($videoPath);

//         // Return the URL to the manifest file
//         $manifestUrl = Storage::url($segmentsDirectory . '/playlist.m3u8');
//         return response()->json(['manifest_url' => $manifestUrl]);
//     }

//     protected function splitVideoIntoSegments($videoPath)
//     {
//         // Define output directory for segments
//         $segmentsDirectory = 'segments/' . pathinfo($videoPath, PATHINFO_FILENAME);

//         // Ensure the segments directory exists
//         Storage::makeDirectory($segmentsDirectory);

//         // Run FFmpeg command to split the video into segments
//         $process = new Process([
//             'ffmpeg',
//             '-i', storage_path('app/' . $videoPath),
//             '-c:v', 'copy',
//             '-c:a', 'copy',
//             '-map', '0',
//             '-f', 'segment',
//             '-segment_time', '10', // 10 seconds per segment
//             '-segment_list', storage_path('app/' . $segmentsDirectory . '/playlist.m3u8'),
//             storage_path('app/' . $segmentsDirectory . '/output_segment_%03d.ts')
//         ]);
//         $process->run();

//         if (!$process->isSuccessful()) {
//             throw new \RuntimeException('Failed to split video into segments');
//         }

//         return $segmentsDirectory;
//     }
// }


    
}
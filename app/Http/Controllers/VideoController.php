<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\HLSService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class VideoController extends Controller
{

    // public function uploadChunks(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'filename'=>'required',
    //         'total_chunks'=>'required',
    //         'chunk'=>'required',
    //         'chunk_index'=>'required',
    //         // 'Advertisement_id'=>'required'
    //     ]);
    //     if($validator->fails()){
    //         return response()->json(['errors'=>$validator->errors()]);
    //     }

    //     $chunk = $request->file('chunk');
    //     $chunkIndex = $request->input('chunk_index');
    //     $totalChunks = $request->input('total_chunks');
    //     $filename = $request->input('filename');

    //     // Define the temporary folder
    //     $chunkTempFolder = storage_path('app/public/tempChunks');
    //     // Create the temporary folder if it doesn't exist
    //     if (!file_exists($chunkTempFolder)) {
    //         mkdir($chunkTempFolder, 0777, true);
    //     }
    //     // Store the chunk data in a temporary file
    //     $chunkFileName = $filename . '-' . $chunkIndex . '.chunk'; // Example filename
    //     file_put_contents($chunkTempFolder . '/' . $chunkFileName, base64_decode($chunk)); // Assuming the chunk data is base64-encoded
    //     if (file_exists($chunkTempFolder . '/' . $chunkFileName)) {
    //         // Check if all chunks are received and assembled
    //         if ($chunkIndex === $totalChunks - 1) {
    //             // Assemble the video file from chunks
    //             $videoPath = $this->assembleVideo($filename, $chunkTempFolder, $totalChunks);
    //             // Clean up the temporary folder
    //             $this->cleanUpTemporaryFolder($chunkTempFolder);
    //             ///////// here we will call on the method for change video format to HLS format & user Id ///////////////
    //             // $HlsData is a array have to variable 1.hlsFormatDirectory 2.manifestFileName to store it in database with specific user Id.
    //             // $HlsData = (new HLSService())->hlsFormat($videoPath);
    //             // $newVideo = new Video();
    //             // $newVideo->hls_format_path = $HlsData['hlsFormatDirectory'];
    //             // $newVideo->manifest_file_name = $HlsData['manifestFileName'];
    //             // $newVideo->owner_id = $request->Advertisement_id;
    //             // $newVideo->save();
    //             return response()->json(['message' => 'Video uploaded successfully!']);
    //         }
    //         // Return success for current chunk, client sends the next chunk
    //         return response()->json(['message' => 'Chunk uploaded successfully']);
    //     } else {
    //         // Handle missing chunk or error
    //         return response()->json(['error' => 'Missing video chunk'], 400);
    //     }
    // }


    public function uploadChunks(Request $request){
        $validator = Validator::make($request->all(), [
           // 'filename'=>'required',
           // 'total_chunks'=>'required',
            'chunk'=>'required',
           // 'chunk_index'=>'required',
            // 'Advertisement_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }
        if ($request->hasFile('chunk')) {
            //$chunk = $request->file('chunk');
            // $chunkIndex = $request->input('chunk_index');
            // $totalChunks = $request->input('total_chunks');
            // $filename = $request->input('filename');
            $chunk = $request->chunk;
            $chunkIndex = 0;
            $totalChunks = 5;
            $filename = "demo";
            $chunkTempFolder = 'public/tempChunks';
            $chunk->storeAs($chunkTempFolder, $filename . '-' . $chunkIndex);
            // Check if all chunks are received and assembled
            if ($chunkIndex === $totalChunks - 1) {
                // Assemble the video file from chunks
                $videoPath = $this->assembleVideo($filename, $chunkTempFolder, $totalChunks);
                // Clean up the temporary folder
                $this->cleanUpTemporaryFolder($chunkTempFolder);
                ///////// here we will call on the method for change video format to HLS format & user Id ///////////////
                // $HlsData is a array have to variable 1.hlsFormatDirectory 2.manifestFileName to store it in database with specific user Id.
                // $HlsData = (new HLSService())->hlsFormat($videoPath);
                // $newVideo = new Video();
                // $newVideo->hls_format_path = $HlsData['hlsFormatDirectory'];
                // $newVideo->manifest_file_name = $HlsData['manifestFileName'];
                // $newVideo->owner_id = $request->Advertisement_id;
                // $newVideo->save();
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
        // Generate a unique filename for the assembled video
        $uniqueFilename = Str::uuid() . '_' . $filename;
        //$uniqueFilename = Carbon::now()->toDateString() . "-" . uniqid() . $filename;

        // Save the assembled video file with the unique filename
        $videoTempFolder = 'public/tempVideos';
        $assembledVideoPath = storage_path("app/{$videoTempFolder}/{$uniqueFilename}.mp4");
        file_put_contents($assembledVideoPath, $assembledVideo);
        return $assembledVideoPath;
    }


    public function cleanUpTemporaryFolder(string $tempFolder){
        // Delete all files in the temporary folder
        $tempFolderPath = storage_path("app/{$tempFolder}");
        $files = scandir($tempFolderPath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink("{$tempFolderPath}/{$file}");
            }
        }
    }





    public function splitVideo(){

    }








    
}
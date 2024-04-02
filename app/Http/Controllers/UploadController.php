<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\HLSService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UploadController extends Controller
{

    public function uploadChunks(Request $request){
        $validator = Validator::make($request->all(), [
            'owner_id'=>'required',
            'total_chunks'=>'required',
            'chunk'=>'required',
            'chunk_index'=>'required',
            //'Advertisement_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }
        if ($request->hasFile('chunk')) {
            $chunk = $request->file('chunk');
            $chunkIndex = (int)$request->input('chunk_index');
            $totalChunks = (int)$request->input('total_chunks');
            $ownerId = $request->input('owner_id');

            $chunkTempFolder = 'public/tempChunks/'. $ownerId;
            if (!file_exists($chunkTempFolder)) {
                mkdir($chunkTempFolder, 0777, true); // Third parameter creates parent directories if they don't exist
            }

            $chunk->storeAs($chunkTempFolder, $ownerId . '-' . $chunkIndex);
            // Check if all chunks are received and assembled
            if ($chunkIndex == $totalChunks - 1) {
                // Assemble the video file from chunks
                $videoPath = $this->assembleVideo($ownerId, $chunkTempFolder, $totalChunks);

                // Clean up the temporary folder
                //$this->cleanUpTemporaryFolder($chunkTempFolder . '/' . $videoPath['ownerId']);

                ///////// here we will call on the method for change video format to HLS format & user Id ///////////////
                // $HlsData is a array have to variable 1.hlsFormatDirectory 2.manifestFileName to store it in database with specific user Id.
                // $HlsData = (new HLSService())->hlsFormat($videoPath['assembledVideoPath']);
                // $advertisement = Advertisement::where('owner_id', $HlsData['ownerId'])->latest()->first(); // to retrieve last advertisement for this owner 
                // $newVideo = new Video();
                // $newVideo->hls_format_path = $HlsData['hlsFormatDirectory'];
                // $newVideo->manifest_file_name = $HlsData['manifestFileName'];
                // $newVideo->owner_id = $advertisement->id;
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


    // public function assembleVideo($ownerId, $chunkTempFolder, int $totalChunks){
    //     // Logic to concatenate all chunks in the temporary folder
    //     // into a single file named $filename in the desired location
    //     $assembledVideo = '';
    //     for ($i = 0; $i < $totalChunks; $i++) {
    //         $chunkPath = storage_path("app/{$chunkTempFolder}/{$ownerId}-{$i}");
    //         $assembledVideo .= file_get_contents($chunkPath);
    //     }
    //      // Get the current timestamp
    //     $timestamp = now();
    //     // Format the timestamp as desired, for example: 'Ymd_His'
    //     $formattedTimestamp = $timestamp->format('Y-m-d_His');
    //     $uniqueFilename = $formattedTimestamp . '(' . $ownerId . ')';
    //     //Save the assembled video file with the unique filename
    //     $videoTempFolder = 'app/public/videoChunks';
    //         if (!file_exists($videoTempFolder)) {
    //             mkdir($videoTempFolder, 0777, true); // Third parameter creates parent directories if they don't exist
    //         }
    //     $assembledVideoPath = storage_path("{$videoTempFolder}/{$uniqueFilename}.mp4");
    //     file_put_contents($assembledVideoPath, $assembledVideo);
    //     $data = ['assembledVideoPath'=>$assembledVideoPath, 'ownerId'=>$ownerId];
    //     return $data;
    // }

    // public function assembleVideo($ownerId, $chunkTempFolder, int $totalChunks){
    //     // Logic to concatenate all chunks in the temporary folder
    //     // into a single file named $filename in the desired location
    //     $assembledVideo = '';
    //     for ($i = 0; $i < $totalChunks; $i++) {
    //         $chunkPath = storage_path("app/{$chunkTempFolder}/{$ownerId}-{$i}");
    //         $assembledVideo .= file_get_contents($chunkPath);
    //     }
    
    //     // Get the current timestamp
    //     $timestamp = now();
    //     // Format the timestamp as desired, for example: 'Ymd_His'
    //     $formattedTimestamp = $timestamp->format('Y-m-d_H:i:s');
    //     $uniqueFilename = $formattedTimestamp . '(' . $ownerId . ')';
        
    //     // Define the directory where the assembled video will be saved
    //     $videoTempFolder = 'public/videoChunks';
        
    //     // Create the directory if it doesn't exist
    //     if (!Storage::exists($videoTempFolder)) {
    //         Storage::makeDirectory($videoTempFolder);
    //     }
        
    //     // Define the path for the assembled video file
    //     $assembledVideoPath = "{$videoTempFolder}/{$uniqueFilename}.mp4";
    
    //     // Save the assembled video file using Storage
    //     Storage::put($assembledVideoPath, $assembledVideo);
    
    //     // Prepare and return the data
    //     $data = ['assembledVideoPath' => $assembledVideoPath, 'ownerId' => $ownerId];
    //     return $data;
    // }
    



    public function assembleVideo($ownerId, $chunkTempFolder, int $totalChunks){
        // Define the directory where the assembled video will be saved
        $videoTempFolder = 'public/tempVideo';
        // Create the directory if it doesn't exist
        if (!Storage::exists($videoTempFolder)) {
            Storage::makeDirectory($videoTempFolder);
        }
        // Get the current timestamp
        $timestamp = now();
        // Format the timestamp as desired, for example: 'Ymd_His'
        $formattedTimestamp = $timestamp->format('Y-m-d_His');
        $uniqueFilename = $formattedTimestamp . '(' . $ownerId . ')';
        $assembledVideoPath = "{$videoTempFolder}/{$uniqueFilename}.mp4";
        // Open the assembled video file for writing
        $assembledVideoFile = Storage::disk('local')->put($assembledVideoPath, '');
        // Iterate through each chunk and append it to the assembled video file
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = storage_path("app/{$chunkTempFolder}/{$ownerId}-{$i}");
            $chunkContents = file_get_contents($chunkPath);
            Storage::disk('local')->append($assembledVideoPath, $chunkContents);
        }
        // Prepare and return the data
        $data = ['assembledVideoPath' => $assembledVideoPath, 'ownerId' => $ownerId];
        return $data;
    }



    public function cleanUpTemporaryFolder(string $tempFolder){
        // Delete all files in the temporary folder
        $tempFolderPath = storage_path("app/{$tempFolder}");
        if (file_exists($tempFolderPath)) {
            $files = scandir($tempFolderPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    unlink("{$tempFolderPath}/{$file}");
                }
            }
            $this->deleteDirectory($tempFolderPath);
        }
    }


    private function deleteDirectory(string $directory) {
        if (!file_exists($directory)) {
            return;
        }
        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $file) {
            $path = $directory . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($directory);
    }






    







    
}
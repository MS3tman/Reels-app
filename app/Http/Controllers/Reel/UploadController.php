<?php

namespace App\Http\Controllers\Reel;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\HLSService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;

class UploadController extends Controller
{
    public function uploadReel(Request $request){

    }


    public function uploadChunks(Request $request){
        $validator = Validator::make($request->all(), [
            'owner_id'=>'required',
            'total_chunks'=>'required',
            'chunk'=>'required',
            'chunk_index'=>'required',
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
                $this->cleanUpTemporaryFolder($chunkTempFolder);

                ///////// here we will call on the method for change video format to HLS format & user Id ///////////////
                // $HlsData is a array have to variable 1.hlsFormatDirectory 2.manifestFileName to store it in database with specific user Id.
                $HlsData = (new HLSService())->hlsFormat($videoPath['assembledVideoPath']);
                // $advertisement = Advertisement::where('owner_id', $HlsData['ownerId'])->latest()->first(); // to retrieve last advertisement for this owner 
                // $newVideo = new Video();
                // $newVideo->hls_format_path = $HlsData['hlsFormatDirectory'];
                // $newVideo->manifest_file_name = $HlsData['manifestFileName'];
                // $newVideo->owner_id = $advertisement->id;
                // $newVideo->save();
                return response()->json(['message' => 'Video uploaded successfully!']);
            }
            //Return success for current chunk, client sends the next chunk
            return response()->json(['message' => 'Chunk uploaded successfully']);
        } else {
            // Handle missing chunk or error
            return response()->json(['error' => 'Missing video chunk'], 400);
        }
    }


    public function assembleVideo($ownerId, $chunkTempFolder, $totalChunks){
        // Get the owner ID and chunk temporary folder from the request
        $ownerId = $ownerId;
        $chunkTempFolder = $chunkTempFolder;

        // Define the directory where the assembled video will be saved
        $videoTempFolder = 'public/tempVideo';

        // Create the directory if it doesn't exist
        if (!Storage::exists($videoTempFolder)) {
            Storage::makeDirectory($videoTempFolder);
        }

        // Generate a unique filename for the assembled video
        $timestamp = now()->format('Y-m-d_His');
        $uniqueFilename = "{$timestamp}_{$ownerId}.mp4";
        $assembledVideoPath = "{$videoTempFolder}/{$uniqueFilename}";

        // Open the assembled video file for writing
        $assembledVideoFile = fopen(storage_path("app/{$assembledVideoPath}"), 'wb');

        // Iterate through each chunk and append it to the assembled video file
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = storage_path("app/{$chunkTempFolder}/{$ownerId}-{$i}");
            $chunkContents = file_get_contents($chunkPath);
            fwrite($assembledVideoFile, $chunkContents);
        }

        // Close the assembled video file
        fclose($assembledVideoFile);

        // Prepare and return the response
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
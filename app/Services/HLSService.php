<?php
namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class HLSService 
{
    public function hlsFormat($videoPath)
    {
        // Extract the filename without the extension
        $fileName = pathinfo($videoPath, PATHINFO_FILENAME);
        
        $reelId = null;
        // Use regular expression to extract the number inside parentheses
        $pattern = '/(\d+)$/';
        if (preg_match($pattern, $fileName, $matches)) {
            $reelId = $matches[1];
        } 
        // Define output directory for HLS files
        $hlsFormatDirectory = 'public/hls/' . $fileName;
        // Ensure the directory exists
        Storage::makeDirectory($hlsFormatDirectory);
        // Define different quality variants (bitrates/resolutions)
        $variants = [
            ['bitrate' => '200k', 'resolution' => '256x144'], // Equivalent to 144p
            ['bitrate' => '400k', 'resolution' => '426x240'], // Equivalent to 240p
            ['bitrate' => '500k', 'resolution' => '640x360'],
            ['bitrate' => '1000k', 'resolution' => '1280x720'],
            ['bitrate' => '2000k', 'resolution' => '1920x1080'],
        ];
        // Array to store variant information for the master playlist
        $variantsInfo = [];
        // Generate normal manifests (media playlists) for each quality variant
        foreach ($variants as $index => $variant) {
            $variantDirectory = "{$hlsFormatDirectory}/variant{$index}";
            Storage::makeDirectory($variantDirectory);
            // Run FFMpeg to generate HLS segments for each quality variant
            $process = new Process([
                env('FFMPEG_BINARIES'),
                //'ffmpeg',
                '-i', storage_path('app/' . $videoPath),
                '-preset', 'fast',
                '-vf', 'scale=' . $variant['resolution'],
                '-b:v', $variant['bitrate'],
                '-c:a', 'aac',
                '-hls_segment_type', 'mpegts',
                '-hls_time', '10', // Segment duration in seconds
                '-hls_list_size', '0',
                '-hls_playlist_type', 'vod',
                storage_path("app/{$variantDirectory}/{$fileName}.m3u8"),
            ]);
            $process->run();
            // Check if FFMpeg process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            // Store variant information for master playlist
            $variantsInfo[] = [
                'bitrate' => $variant['bitrate'],
                'resolution' => $variant['resolution'],
                'url' => url(Storage::url("{$variantDirectory}/{$fileName}.m3u8")),
            ];
        }
        // Generate master playlist (master manifest)
        $masterPlaylistContent = "#EXTM3U\n#EXT-X-VERSION:3\n";
        foreach ($variantsInfo as $variant) {
            $masterPlaylistContent .= "#EXT-X-STREAM-INF:BANDWIDTH={$variant['bitrate']},RESOLUTION={$variant['resolution']}\n";
            $masterPlaylistContent .= "{$variant['url']}\n";
        }
        Storage::put("{$hlsFormatDirectory}/master.m3u8", $masterPlaylistContent);
        // Return data about generated HLS files
        return [
            'hlsFormatDirectory' => $hlsFormatDirectory,
            'fileName'=>$fileName,  // to remove video file mp4
            'reelId'=>$reelId,
            //'masterUrl' => url(Storage::url("app/{$hlsFormatDirectory}/master.m3u8")),
        ];
    }

}


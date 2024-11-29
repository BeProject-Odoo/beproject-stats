<?php

namespace Beproject\Monitor\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class BeprojectStatsController extends Controller
{
    // token must be at least 8 consecutive characters from the APP_KEY
    public function checkRequestAuth(Request $request) {
        if($request->has("token") && strlen($request->token) > 8 && str_contains(env("APP_KEY"), $request->token)) {
            return true;
        }
        abort(501, 'token invalid');
    }

    public function ping(Request $request): JsonResponse
    {
        $this->checkRequestAuth($request);
        try {
            DB::connection()->getPdo();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database connection failed'], 500);
        }        
    }

    public function checkSystemHealth(Request $request): JsonResponse
    {
        $this->checkRequestAuth($request);
        $status = [
            'success' => true,
            'database' => $this->checkDatabase(),
            'disk_space' => Cache::remember("disk_space", 600, function () {return $this->checkDiskSpace();}),
        ];
        return response()->json($status);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            
            return [
                'status' => 'connected',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkDiskSpace(): array
    {
        $disks = [];
        
        // Get all mounted filesystems
        $filesystems = array_filter(explode("\n", trim(shell_exec('df -P'))));
        
        // Remove header
        array_shift($filesystems);
        
        foreach ($filesystems as $filesystem) {
            $parts = preg_split('/\s+/', trim($filesystem));
            
            if (count($parts) >= 6) {
                $disks[] = [
                    'filesystem' => $parts[0],
                    'mount_point' => $parts[5],
                    'total_space' => $this->formatBytes($parts[1] * 1024), // Convert KB to bytes
                    'used_space' => $this->formatBytes($parts[2] * 1024),
                    'available_space' => $this->formatBytes($parts[3] * 1024),
                    'use_percentage' => $parts[4]
                ];
            }
        }
        
        return $disks;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

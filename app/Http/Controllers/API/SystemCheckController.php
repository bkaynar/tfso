<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SystemCheckController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            $systemInfo = [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'system' => [
                    'memory' => $this->getMemoryUsage(),
                    'cpu' => $this->getCpuUsage(),
                    'disk' => $this->getDiskUsage(),
                ],
                'controllers' => $this->checkControllers(),
                'server' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                ]
            ];

            return response()->json($systemInfo);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'System check failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    private function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit !== '-1') {
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            $memoryUsagePercent = round(($memoryUsage / $memoryLimitBytes) * 100, 2);
        } else {
            $memoryUsagePercent = null;
        }

        return [
            'current' => $this->formatBytes($memoryUsage),
            'current_bytes' => $memoryUsage,
            'peak' => $this->formatBytes($memoryPeak),
            'peak_bytes' => $memoryPeak,
            'limit' => $memoryLimit,
            'usage_percent' => $memoryUsagePercent,
        ];
    }

    private function getCpuUsage(): array
    {
        $load = null;
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
        }

        return [
            'load_average' => $load,
            'load_1min' => $load[0] ?? null,
            'load_5min' => $load[1] ?? null,
            'load_15min' => $load[2] ?? null,
        ];
    }

    private function getDiskUsage(): array
    {
        try {
            $currentPath = getcwd() ?: base_path();
            
            $totalBytes = disk_total_space($currentPath);
            $freeBytes = disk_free_space($currentPath);
            
            if ($totalBytes === false || $freeBytes === false) {
                return [
                    'status' => 'unavailable',
                    'message' => 'Disk usage information not available due to server restrictions',
                ];
            }
            
            $usedBytes = $totalBytes - $freeBytes;
            $usagePercent = $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 2) : 0;

            return [
                'total' => $this->formatBytes($totalBytes),
                'total_bytes' => $totalBytes,
                'used' => $this->formatBytes($usedBytes),
                'used_bytes' => $usedBytes,
                'free' => $this->formatBytes($freeBytes),
                'free_bytes' => $freeBytes,
                'usage_percent' => $usagePercent,
                'path_checked' => $currentPath,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to retrieve disk usage',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function checkControllers(): array
    {
        $controllers = [];
        $controllerPath = app_path('Http/Controllers');
        
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($controllerPath)
            );

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($controllerPath . '/', '', $file->getPathname());
                    $className = str_replace(['/', '.php'], ['\\', ''], $relativePath);
                    $fullClassName = 'App\\Http\\Controllers\\' . $className;
                    
                    $status = 'ok';
                    $error = null;
                    
                    try {
                        if (class_exists($fullClassName)) {
                            $reflection = new \ReflectionClass($fullClassName);
                            if (!$reflection->isInstantiable()) {
                                $status = 'not_instantiable';
                            }
                        } else {
                            $status = 'class_not_found';
                        }
                    } catch (\Exception $e) {
                        $status = 'error';
                        $error = $e->getMessage();
                    }
                    
                    $controllers[] = [
                        'name' => $className,
                        'file' => $relativePath,
                        'status' => $status,
                        'error' => $error,
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to scan controllers',
                'error' => $e->getMessage(),
            ];
        }

        $totalControllers = count($controllers);
        $healthyControllers = count(array_filter($controllers, fn($c) => $c['status'] === 'ok'));
        
        return [
            'total' => $totalControllers,
            'healthy' => $healthyControllers,
            'unhealthy' => $totalControllers - $healthyControllers,
            'health_percentage' => $totalControllers > 0 ? round(($healthyControllers / $totalControllers) * 100, 2) : 100,
            'controllers' => $controllers,
        ];
    }

    private function formatBytes(int $size, int $precision = 2): string
    {
        if ($size === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        $index = floor($base);
        
        return round(pow(1024, $base - $index), $precision) . ' ' . $units[$index];
    }

    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $number = (int) $value;
        
        switch ($last) {
            case 'g':
                $number *= 1024;
            case 'm':
                $number *= 1024;
            case 'k':
                $number *= 1024;
        }
        
        return $number;
    }
}

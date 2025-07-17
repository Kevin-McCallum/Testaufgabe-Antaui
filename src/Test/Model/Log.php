<?php

namespace Test\Model;

class Log
{
    private $csvPath;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Log-Datei existiert nicht: $filePath");
        }
        if (!is_writable($filePath)) {
            throw new \Exception("Log-Datei ist nicht beschreibbar: $filePath");
        }
        $this->csvPath = $filePath;
    }

    public function write(string $username, string $message): bool
    {
        $line = [
            date('Y-m-d H:i:s'),
            $username,
            $message
        ];

        $fp = fopen($this->csvPath, 'a');
        if (!$fp) {
            return false;
        }

        fputcsv($fp, $line);
        fclose($fp);
        return true;
    }

    public function getLogsByUsername(string $username): array
    {
        $logs = [];

        if (!file_exists($this->csvPath)) {
            return $logs;
        }

        if (($fp = fopen($this->csvPath, 'r')) !== false) {
            while (($data = fgetcsv($fp)) !== false) {
                if (isset($data[1]) && $data[1] === $username) {
                    $logs[] = [
                        'timestamp' => $data[0] ?? '',
                        'username' => $data[1],
                        'message' => $data[2] ?? '',
                    ];
                }
            }
            fclose($fp);
        }

        return $logs;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProcessedDataExport;


class FileManipulationController extends Controller
{
    public function index()
    {
        return view('file-manipulation.index');
    }

    public function processFile(Request $request)
    {
        // Security: File validation
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240', // 10MB max
            'order_by' => 'required|in:alphabetical,length'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('file');
            $orderBy = $request->order_by;

            // Read file based on type
            $data = $this->readFile($file);

            if (empty($data)) {
                return redirect()->back()->with('error', 'The file appears to be empty or invalid.');
            }

            // Security: Validate and sanitize data
            $sanitizedData = $this->sanitizeData($data);

            // Remove duplicates
            $uniqueData = array_unique($sanitizedData);

            // Sort data
            $sortedData = $this->sortData($uniqueData, $orderBy);

            // Generate output file
            $fileName = 'processed_data_' . time() . '.xlsx';
            Excel::store(new ProcessedDataExport($sortedData), $fileName, 'public');

            return response()->download(
                storage_path('app/public/' . $fileName)
            )->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    private function readFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $data = [];

        switch ($extension) {
            case 'csv':
                $data = $this->readCSV($file);
                break;
            case 'txt':
                $data = $this->readTXT($file);
                break;
            case 'xlsx':
                $data = $this->readXLSX($file);
                break;
        }

        return $data;
    }

    private function readCSV($file)
    {
        $data = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!empty($row[0])) {
                    $data[] = $row[0];
                }
            }
            fclose($handle);
        }
        return $data;
    }

    private function readTXT($file)
    {
        $content = file_get_contents($file->getPathname());
        $lines = explode("\n", $content);
        
        return array_filter(array_map('trim', $lines), function($line) {
            return !empty($line);
        });
    }

    private function readXLSX($file)
    {
        $data = Excel::toArray([], $file);
        $result = [];
        
        foreach ($data[0] as $row) {
            if (!empty($row[0])) {
                $result[] = $row[0];
            }
        }
        
        return $result;
    }

    private function sanitizeData($data)
    {
        return array_map(function($item) {
            // Security: Prevent XSS and sanitize input
            $sanitized = strip_tags($item);
            $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
            return trim($sanitized);
        }, $data);
    }

    private function sortData($data, $orderBy)
    {
        if ($orderBy === 'alphabetical') {
            sort($data, SORT_STRING | SORT_FLAG_CASE);
        } else {
            usort($data, function($a, $b) {
                return strlen($a) - strlen($b);
            });
        }

        return $data;
    }
}
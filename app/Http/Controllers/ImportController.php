<?php

namespace App\Http\Controllers;

use App\Models\CompanyStatistic;
use App\Models\Period;
use App\Models\Company;
use App\Models\PermitType;
use App\Models\PermitStatistic;
use App\Models\NearMiss;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportController extends Controller
{
    /**
     * Show import page for safety metrics
     */
    public function showSafetyMetricsImport()
    {
        $companies = Company::all();
        return view('imports.safety-metrics', compact('companies'));
    }

    /**
     * Show import page for work permits
     */
    public function showWorkPermitImport()
    {
        $companies = Company::all();
        $permitTypes = PermitType::all();
        return view('imports.work-permit', compact('companies', 'permitTypes'));
    }

    /**
     * Show import page for near miss
     */
    public function showNearMissImport()
    {
        $companies = Company::all();
        $departments = Department::all();
        return view('imports.near-miss', compact('companies', 'departments'));
    }

    /**
     * Process safety metrics import
     */
    public function importSafetyMetrics(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'company_id' => 'required|exists:companies,id'
        ]);

        try {
            $file = $request->file('file');
            $rows = array_map('str_getcsv', file($file->path()));
            
            // Skip header row
            $header = array_shift($rows);
            
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    if (empty($row[0])) continue;

                    // Expected columns: month, year, man_hours, employee, lta, lost_work_days, lost_time, kecelakaan_kerja
                    $month = (int)$row[0];
                    $year = (int)$row[1];
                    $manHours = (float)$row[2] ?? 0;
                    $employee = (int)$row[3] ?? 0;
                    $lta = (int)$row[4] ?? 0;
                    $lostWorkDays = (int)$row[5] ?? 0;
                    $lostTime = (int)$row[6] ?? 0;
                    $kecelakaanKerja = (int)$row[7] ?? 0;

                    // Validate month and year
                    if ($month < 1 || $month > 12) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid month. Must be 1-12";
                        continue;
                    }

                    if ($year < 2000 || $year > date('Y') + 1) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid year";
                        continue;
                    }

                    // Find or create period
                    $period = Period::firstOrCreate(
                        ['month' => $month, 'year' => $year],
                        ['month' => $month, 'year' => $year]
                    );

                    // Update or create statistic
                    CompanyStatistic::updateOrCreate(
                        [
                            'company_id' => $request->company_id,
                            'period_id' => $period->id
                        ],
                        [
                            'man_hours' => $manHours,
                            'employee' => $employee,
                            'lta' => $lta,
                            'lost_work_days' => $lostWorkDays,
                            'lost_time' => $lostTime,
                            'kecelakaan_kerja' => $kecelakaanKerja
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported $imported records",
                'imported' => $imported,
                'errors' => $errors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process work permit import
     */
    public function importWorkPermit(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'company_id' => 'required|exists:companies,id'
        ]);

        try {
            $file = $request->file('file');
            $rows = array_map('str_getcsv', file($file->path()));
            
            // Skip header row
            $header = array_shift($rows);
            
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    if (empty($row[0])) continue;

                    // Expected columns: permit_type_id, month, year, total
                    $permitTypeId = (int)$row[0];
                    $month = (int)$row[1];
                    $year = (int)$row[2];
                    $total = (int)$row[3] ?? 0;

                    // Validate
                    if (!PermitType::find($permitTypeId)) {
                        $errors[] = "Row " . ($index + 2) . ": Permit type not found";
                        continue;
                    }

                    if ($month < 1 || $month > 12) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid month";
                        continue;
                    }

                    if ($year < 2000 || $year > date('Y') + 1) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid year";
                        continue;
                    }

                    // Find or create period
                    $period = Period::firstOrCreate(
                        ['month' => $month, 'year' => $year],
                        ['month' => $month, 'year' => $year]
                    );

                    // Update or create statistic
                    PermitStatistic::updateOrCreate(
                        [
                            'company_id' => $request->company_id,
                            'permit_type_id' => $permitTypeId,
                            'period_id' => $period->id
                        ],
                        [
                            'total' => $total
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported $imported records",
                'imported' => $imported,
                'errors' => $errors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process near miss import
     */
    public function importNearMiss(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'company_id' => 'required|exists:companies,id'
        ]);

        try {
            $file = $request->file('file');
            $rows = array_map('str_getcsv', file($file->path()));
            
            // Skip header row
            $header = array_shift($rows);
            
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    if (empty($row[0])) continue;

                    // Expected columns: date, location, department_id, category, severity, likelihood, description, status
                    $date = Carbon::createFromFormat('Y-m-d', $row[0]);
                    $location = $row[1] ?? '';
                    $departmentId = (int)$row[2];
                    $category = $row[3] ?? '';
                    $severity = $row[4] ?? 'Low';
                    $likelihood = $row[5] ?? 'Low';
                    $description = $row[6] ?? '';
                    $status = $row[7] ?? 'Open';

                    // Validate department
                    if (!Department::find($departmentId)) {
                        $errors[] = "Row " . ($index + 2) . ": Department not found";
                        continue;
                    }

                    // Validate category
                    if (!in_array($category, ['Unsafe Act', 'Unsafe Condition'])) {
                        $errors[] = "Row " . ($index + 2) . ": Invalid category";
                        continue;
                    }

                    // Create near miss
                    NearMiss::create([
                        'company_id' => $request->company_id,
                        'period_id' => null,
                        'department_id' => $departmentId,
                        'date' => $date,
                        'location' => $location,
                        'category' => $category,
                        'severity' => $severity,
                        'likelihood' => $likelihood,
                        'risk_level' => $this->calculateRiskLevel($severity, $likelihood),
                        'description' => $description,
                        'action_required' => '',
                        'status' => $status
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported $imported records",
                'imported' => $imported,
                'errors' => $errors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Calculate risk level based on severity and likelihood
     */
    private function calculateRiskLevel($severity, $likelihood)
    {
        $matrix = [
            'High' => ['High' => 'High', 'Medium' => 'High', 'Low' => 'Medium'],
            'Medium' => ['High' => 'High', 'Medium' => 'Medium', 'Low' => 'Low'],
            'Low' => ['High' => 'Medium', 'Medium' => 'Low', 'Low' => 'Low']
        ];

        return $matrix[$severity][$likelihood] ?? 'Medium';
    }

    /**
     * Download sample CSV for safety metrics
     */
    public function downloadSafetyMetricsSample()
    {
        $filename = 'safety_metrics_sample.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        );

        $columns = ['Month', 'Year', 'Man Hours', 'Employee', 'LTA', 'Lost Work Days', 'Lost Time', 'Kecelakaan Kerja'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add sample row
            fputcsv($file, [1, 2024, 1000, 50, 0, 0, 0, 0]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download sample CSV for work permit
     */
    public function downloadWorkPermitSample()
    {
        $filename = 'work_permit_sample.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        );

        $columns = ['Permit Type ID', 'Month', 'Year', 'Total'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add sample row
            fputcsv($file, [1, 1, 2024, 10]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download sample CSV for near miss
     */
    public function downloadNearMissSample()
    {
        $filename = 'near_miss_sample.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        );

        $columns = ['Date', 'Location', 'Department ID', 'Category', 'Severity', 'Likelihood', 'Description', 'Status'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add sample row
            fputcsv($file, ['2024-01-15', 'Workshop', 1, 'Unsafe Act', 'Medium', 'High', 'Sample incident', 'Open']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

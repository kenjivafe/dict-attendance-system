<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CSCForm48 extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reports';

    protected static string $view = 'filament.pages.csc-form-48';

    protected static ?string $title = 'Form 48 Generator';

    protected static ?string $slug = 'csc-form-generator';

    public ?int $selectedUser = null;
    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;
    public ?int $totalUndertimeHours = null;
    public ?int $totalUndertimeMinutes = null;
    public array $dtrData = [];

    public function mount()
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedUser', 'selectedMonth', 'selectedYear'])) {
            $this->fetchAttendance();
        }
    }

    public function fetchAttendance()
    {
        if ($this->selectedUser && $this->selectedMonth && $this->selectedYear) {
            $attendances = Attendance::where('user_id', $this->selectedUser)
                ->whereMonth('date', $this->selectedMonth)
                ->whereYear('date', $this->selectedYear)
                ->orderBy('date')
                ->get();

            // Store individual attendance records
            $this->dtrData = $attendances->map(function ($record) {
                return [
                    'day' => date('j', strtotime($record->date)),
                    'time_in_am' => $record->time_in_am ?? '',
                    'time_out_am' => $record->time_out_am ?? '',
                    'time_in_pm' => $record->time_in_pm ?? '',
                    'time_out_pm' => $record->time_out_pm ?? '',
                    'undertime_hours' => $record->undertime_hours ?? 0,
                    'undertime_minutes' => $record->undertime_minutes ?? 0,
                ];
            })->toArray();

            // Calculate total undertime
            $this->totalUndertimeHours = $attendances->sum('undertime_hours');
            $this->totalUndertimeMinutes = $attendances->sum('undertime_minutes');

            // Convert minutes into hours if 60 minutes are reached
            if ($this->totalUndertimeMinutes >= 60) {
                $this->totalUndertimeHours += intdiv($this->totalUndertimeMinutes, 60);
                $this->totalUndertimeMinutes = $this->totalUndertimeMinutes % 60;
            }
        } else {
            $this->dtrData = [];
            $this->totalUndertimeHours = 0;
            $this->totalUndertimeMinutes = 0;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('DTR Filter')
                    ->description('Select employee and date to generate DTR accordingly')
                    ->icon('heroicon-o-funnel')
                    ->columns(['xs' => 3, 'md' => 3, 'xl' => 1])
                    ->schema([
                        Select::make('selectedUser')
                            ->label('Select Employee')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        Select::make('selectedMonth')
                            ->label('Select Month')
                            ->options([
                                '01' => 'January', '02' => 'February', '03' => 'March',
                                '04' => 'April', '05' => 'May', '06' => 'June',
                                '07' => 'July', '08' => 'August', '09' => 'September',
                                '10' => 'October', '11' => 'November', '12' => 'December',
                            ])
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->live()
                            ->columnSpan(1),
                        Select::make('selectedYear')
                            ->label('Select Year')
                            ->options(array_combine(
                                range(date('Y'), 2000),
                                range(date('Y'), 2000)
                            ))
                            ->required()
                            ->native(false)
                            ->live()
                            ->columnSpan(1),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // ActionGroup::make([
                // Action::make('download_dtr')
                //     ->label('Export to PDF')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->color('primary')
                //     ->action(fn () => $this->downloadDTR()),
                Action::make('Export to Excel')
                    ->label('Export Spreadsheet')
                    ->action('downloadDTRExcel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success'),
            // ])
            // ->label('Export')->button()
            // ->icon('heroicon-o-arrow-down-tray')
        ];
    }

    public function downloadDTR()
    {
        if (!$this->selectedUser) {
            Notification::make()
                ->title('Error')
                ->body('Please select an employee before downloading.')
                ->danger()
                ->send();
            return;
        }

        // Fetch user details
        $user = User::find($this->selectedUser);

        // Convert month number to month name
        $monthName = date('F', mktime(0, 0, 0, $this->selectedMonth, 1));

        // Generate the PDF
        $pdf = Pdf::loadView('pdfs.csc-form-48', [
            'user' => $user,
            'userName' => $user?->name ?? '',  // ✅ User's name
            'month' => $this->selectedMonth,
            'monthName' => $monthName,  // ✅ Month name
            'year' => $this->selectedYear ?? '20',
            'dtrData' => $this->dtrData,  // ✅ Attendance data
            'totalUndertimeHours' => $this->totalUndertimeHours ?? 0,  // ✅ Total undertime hours
            'totalUndertimeMinutes' => $this->totalUndertimeMinutes ?? 0,  // ✅ Total undertime minutes
        ])
        ->setPaper('a4', 'portrait');

        return response()->streamDownload(fn () => print($pdf->output()), 'DTR.pdf');
    }

    public function downloadDTRExcel()
    {
        if (!$this->selectedUser) {
            Notification::make()
                ->title('Error')
                ->body('Please select an employee before downloading.')
                ->danger()
                ->send();
            return;
        }

        // Fetch user details
        $user = User::find($this->selectedUser);

        // Convert month number to month name
        $monthName = date('F', mktime(0, 0, 0, $this->selectedMonth, 1));

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $topMargin = 1 * 0.393701; //cm
        $bottomMargin = 1 * 0.393701; //cm
        $leftMargin = 1 * 0.393701; //cm
        $rightMargin = 1 * 0.393701; //cm

        $sheet->getPageMargins()->setTop($topMargin);
        $sheet->getPageMargins()->setBottom($bottomMargin);
        $sheet->getPageMargins()->setLeft($leftMargin);
        $sheet->getPageMargins()->setRight($rightMargin);

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4); // Set to A4 size

        // OR Set zoom scale (Percentage)
        $sheet->getPageSetup()->setScale(90); // 80% zoom

        $sheet->getColumnDimension('A')->setWidth(0.88);
        $sheet->getColumnDimension('B')->setWidth(4.89);
        foreach (range('C', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(7);
        }
        $sheet->getColumnDimension('J')->setWidth(0.88);
        $sheet->getColumnDimension('K')->setWidth(4.89);
        foreach (range('L', 'Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(7);
        }
        $sheet->getRowDimension('2')->setRowHeight(7);
        $sheet->getRowDimension('4')->setRowHeight(7);
        $sheet->getRowDimension('7')->setRowHeight(7);
        $sheet->getRowDimension('11')->setRowHeight(7);
        $sheet->getRowDimension('12')->setRowHeight(25);
        $sheet->getRowDimension('13')->setRowHeight(25);



        // SET TITLE 1
        $sheet->setCellValue('B1', 'Civil Service Form No. 48');
        $sheet->mergeCells('B1:D1');
        $sheet->getStyle('B1')->getFont()->setItalic(true)->setSize(8);

        $sheet->setCellValue('B3', 'DAILY TIME RECORD');
        $sheet->mergeCells('B3:H3');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal('center');

        // SET TITLE 2
        $sheet->setCellValue('K1', 'Civil Service Form No. 48');
        $sheet->mergeCells('K1:M1');
        $sheet->getStyle('K1')->getFont()->setItalic(true)->setSize(8);

        $sheet->setCellValue('K3', 'DAILY TIME RECORD');
        $sheet->mergeCells('K3:Q3');
        $sheet->getStyle('K3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('K3')->getAlignment()->setHorizontal('center');



        // Set User Info 1
        $sheet->setCellValue('B5', $user->name);
        $sheet->setCellValue('B6', '(Name)');
        $sheet->mergeCells('B5:H5');
        $sheet->mergeCells('B6:H6');
        $sheet->getStyle('B5')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('B5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B6')->getFont()->setItalic(true)->setBold(true)->setSize(11);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal('center');

        // Set User Info 2
        $sheet->setCellValue('K5', $user->name);
        $sheet->setCellValue('K6', '(Name)');
        $sheet->mergeCells('K5:Q5');
        $sheet->mergeCells('K6:Q6');
        $sheet->getStyle('K5')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('K5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('K6')->getFont()->setItalic(true)->setBold(true)->setSize(11);
        $sheet->getStyle('K6')->getAlignment()->setHorizontal('center');



        // For the month of 1
        $sheet->mergeCells('B8:D8');
        $sheet->mergeCells('E8:F8');
        $sheet->setCellValue('B8', 'For the month of ');
        $sheet->setCellValue('E8', $monthName . ',');
        $sheet->getStyle('B8')->getFont()->setItalic(true);
        $sheet->getStyle('E8')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('H8', $this->selectedYear);
        $sheet->getStyle('H8')->getAlignment()->setHorizontal('center');

        // For the month of 2
        $sheet->mergeCells('K8:M8');
        $sheet->mergeCells('N8:O8');
        $sheet->setCellValue('K8', 'For the month of ');
        $sheet->setCellValue('N8', $monthName . ',');
        $sheet->getStyle('K8')->getFont()->setItalic(true);
        $sheet->getStyle('N8')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('Q8', $this->selectedYear);
        $sheet->getStyle('Q8')->getAlignment()->setHorizontal('center');



        // Official hours of arrival 1
        $sheet->mergeCells('B9:D9');
        $sheet->mergeCells('E9:F9');
        $sheet->mergeCells('G9:H9');
        $sheet->setCellValue('B9', 'Official hours of arrival');
        $sheet->setCellValue('E9', 'Regular Days');
        $sheet->getStyle('B9')->getFont()->setItalic(true);
        $sheet->getStyle('E9')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('E9')->getFont()->setItalic(true);

        // Official hours of arrival 2
        $sheet->mergeCells('K9:M9');
        $sheet->mergeCells('N9:O9');
        $sheet->mergeCells('P9:Q9');
        $sheet->setCellValue('K9', 'Official hours of arrival');
        $sheet->setCellValue('N9', 'Regular Days');
        $sheet->getStyle('K9')->getFont()->setItalic(true);
        $sheet->getStyle('N9')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('N9')->getFont()->setItalic(true);



        //and departure 1
        $sheet->mergeCells('C10:D10');
        $sheet->mergeCells('E10:F10');
        $sheet->mergeCells('G10:H10');
        $sheet->setCellValue('C10', 'and departure');
        $sheet->setCellValue('E10', 'Saturdays');
        $sheet->getStyle('C10')->getFont()->setItalic(true);
        $sheet->getStyle('E10')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('E10')->getFont()->setItalic(true);

        //and departure 2
        $sheet->mergeCells('L10:M10');
        $sheet->mergeCells('N10:O10');
        $sheet->mergeCells('P10:Q10');
        $sheet->setCellValue('L10', 'and departure');
        $sheet->setCellValue('N10', 'Saturdays');
        $sheet->getStyle('L10')->getFont()->setItalic(true);
        $sheet->getStyle('N10')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('N10')->getFont()->setItalic(true);




        //AM PM UNDERTIME 1
        $sheet->mergeCells('B12:B13');
        $sheet->mergeCells('C12:D12');
        $sheet->mergeCells('E12:F12');
        $sheet->mergeCells('G12:H12');

        $sheet->setCellValue('B12', 'Days');
        $sheet->getStyle('B12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B12')->getFont()->setBold(true)->setSize(11);

        $rootHeaders = ['A.M.', '', 'P.M.', '', 'UNDERTIME'];
        $sheet->fromArray($rootHeaders, null, 'C12');
        $sheet->getStyle('C12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('C12')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('E12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E12')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('G12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G12')->getFont()->setBold(true)->setSize(11);


        //AM PM UNDERTIME 2
        $sheet->mergeCells('K12:K13');
        $sheet->mergeCells('L12:M12');
        $sheet->mergeCells('N12:O12');
        $sheet->mergeCells('P12:Q12');

        $sheet->setCellValue('K12', 'Days');
        $sheet->getStyle('K12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('K12')->getFont()->setBold(true)->setSize(11);

        $rootHeaders = ['A.M.', '', 'P.M.', '', 'UNDERTIME'];
        $sheet->fromArray($rootHeaders, null, 'L12');
        $sheet->getStyle('L12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('L12')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('N12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('N12')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('P12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('P12')->getFont()->setBold(true)->setSize(11);




        // Set Headers1
        $headers = ['Arrival', 'Departure', 'Arrival', 'Departure', 'Hours', 'Minutes'];
        $sheet->fromArray($headers, null, 'C13');

        // Apply bold style to headers 1
        $sheet->getStyle('C13:H13')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('C13:H13')->getAlignment()->setVertical('center')->setHorizontal('center');


        // Set Headers 2
        $headers = ['Arrival', 'Departure', 'Arrival', 'Departure', 'Hours', 'Minutes'];
        $sheet->fromArray($headers, null, 'L13');

        // Apply bold style to headers 2
        $sheet->getStyle('L13:Q13')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('L13:Q13')->getAlignment()->setVertical('center')->setHorizontal('center');




        // dd($this->dtrData);
        // Populate Attendance Data
        $row = 14;
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $this->selectedMonth, $this->selectedYear);

        for ($day = 1; $day <= 31; $day++) {
            $date = sprintf('%04d-%02d-%02d', $this->selectedYear, $this->selectedMonth, $day);
            $isValidDay = ($day <= $totalDays);
            $dayOfWeek = $isValidDay ? date('N', strtotime($date)) : null; // 1 (Monday) to 7 (Sunday)
            $dtr = collect($this->dtrData)->firstWhere('day', $day) ?? [];

            // Set the day number
            $sheet->setCellValue('B' . $row, $day);
            $sheet->setCellValue('K' . $row, $day); // Duplicate for second section

            if (!$isValidDay) {
                // Leave blank for invalid days (e.g., Feb 29-31)
                foreach (['C', 'D', 'E', 'F', 'G', 'H', 'L', 'M', 'N', 'O', 'P', 'Q'] as $col) {
                    $sheet->setCellValue($col . $row, '');
                }
            } elseif ($dayOfWeek == 6 || $dayOfWeek == 7) { // Weekend (Saturday or Sunday)
                // Check if there's attendance data for this day
                if (!empty($dtr)) {
                    // Populate actual time data if available
                    $sheet->setCellValue('C' . $row, !empty($dtr['time_in_am']) ? date('H:i', strtotime($dtr['time_in_am'])) : '');
                    $sheet->setCellValue('D' . $row, !empty($dtr['time_out_am']) ? date('H:i', strtotime($dtr['time_out_am'])) : '');
                    $sheet->setCellValue('E' . $row, !empty($dtr['time_in_pm']) ? date('H:i', strtotime($dtr['time_in_pm'])) : '');
                    $sheet->setCellValue('F' . $row, !empty($dtr['time_out_pm']) ? date('H:i', strtotime($dtr['time_out_pm'])) : '');
                    $sheet->setCellValue('G' . $row, $dtr['undertime_hours'] ?? '');
                    $sheet->setCellValue('H' . $row, $dtr['undertime_minutes'] ?? '');
                    
                    // Duplicate for the second section (K-Q)
                    $sheet->setCellValue('L' . $row, !empty($dtr['time_in_am']) ? date('H:i', strtotime($dtr['time_in_am'])) : '');
                    $sheet->setCellValue('M' . $row, !empty($dtr['time_out_am']) ? date('H:i', strtotime($dtr['time_out_am'])) : '');
                    $sheet->setCellValue('N' . $row, !empty($dtr['time_in_pm']) ? date('H:i', strtotime($dtr['time_in_pm'])) : '');
                    $sheet->setCellValue('O' . $row, !empty($dtr['time_out_pm']) ? date('H:i', strtotime($dtr['time_out_pm'])) : '');
                    $sheet->setCellValue('P' . $row, $dtr['undertime_hours'] ?? '');
                    $sheet->setCellValue('Q' . $row, $dtr['undertime_minutes'] ?? '');
                } else {
                    // Display 'Sat' or 'Sun' if no attendance records
                    $weekendLabel = ($dayOfWeek == 6) ? 'Sat' : 'Sun';
                    foreach (['C', 'D', 'E', 'F', 'L', 'M', 'N', 'O'] as $col) {
                        $sheet->setCellValue($col . $row, $weekendLabel);
                    }
                    foreach (['G', 'H', 'P', 'Q'] as $col) { // No undertime for weekends
                        $sheet->setCellValue($col . $row, '');
                    }
                }
            } else {
                // Populate actual data for weekdays
                $sheet->setCellValue('C' . $row, !empty($dtr['time_in_am']) ? date('H:i', strtotime($dtr['time_in_am'])) : '');
                $sheet->setCellValue('D' . $row, !empty($dtr['time_out_am']) ? date('H:i', strtotime($dtr['time_out_am'])) : '');
                $sheet->setCellValue('E' . $row, !empty($dtr['time_in_pm']) ? date('H:i', strtotime($dtr['time_in_pm'])) : '');
                $sheet->setCellValue('F' . $row, !empty($dtr['time_out_pm']) ? date('H:i', strtotime($dtr['time_out_pm'])) : '');
                $sheet->setCellValue('G' . $row, $dtr['undertime_hours'] ?? '');
                $sheet->setCellValue('H' . $row, $dtr['undertime_minutes'] ?? '');

                // Duplicate for the second section (K-Q)
                $sheet->setCellValue('L' . $row, !empty($dtr['time_in_am']) ? date('H:i', strtotime($dtr['time_in_am'])) : '');
                $sheet->setCellValue('M' . $row, !empty($dtr['time_out_am']) ? date('H:i', strtotime($dtr['time_out_am'])) : '');
                $sheet->setCellValue('N' . $row, !empty($dtr['time_in_pm']) ? date('H:i', strtotime($dtr['time_in_pm'])) : '');
                $sheet->setCellValue('O' . $row, !empty($dtr['time_out_pm']) ? date('H:i', strtotime($dtr['time_out_pm'])) : '');
                $sheet->setCellValue('P' . $row, $dtr['undertime_hours'] ?? '');
                $sheet->setCellValue('Q' . $row, $dtr['undertime_minutes'] ?? '');
            }

            // Set row height and center alignment
            $sheet->getRowDimension($row)->setRowHeight(17.5);
            $sheet->getStyle("B{$row}:H{$row}")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("K{$row}:Q{$row}")->getAlignment()->setHorizontal('center');

            // Apply time format to C-F and L-O for valid days with attendance data
            if ($isValidDay && (!empty($dtr) || ($dayOfWeek != 6 && $dayOfWeek != 7))) {
                foreach (['C', 'D', 'E', 'F', 'L', 'M', 'N', 'O'] as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('hh:mm');
                }
            }

            // Apply bottom dashed border to B-H and K-Q
            foreach (['C', 'D', 'E', 'F', 'G', 'H', 'L', 'M', 'N', 'O', 'P', 'Q'] as $col) {
                $sheet->getStyle("{$col}{$row}")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DASHED,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }

            $row++;
        }

        // total 1
        $sheet->getRowDimension('45')->setRowHeight(7);
        $sheet->setCellValue('C46', 'TOTAL');
        $sheet->getStyle('C46')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('C46')->getFont()->setBold(true)->setSize(9);
        $sheet->getRowDimension('47')->setRowHeight(7);

        // total 2
        $sheet->setCellValue('L46', 'TOTAL');
        $sheet->getStyle('L46')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('L46')->getFont()->setBold(true)->setSize(9);


        // Calculate total undertime
        $totalUndertimeHours = array_sum(array_column($this->dtrData, 'undertime_hours'));
        $totalUndertimeMinutes = array_sum(array_column($this->dtrData, 'undertime_minutes'));

        // Convert excess minutes to hours if minutes exceed 60
        $extraHours = intdiv($totalUndertimeMinutes, 60);
        $totalUndertimeHours += $extraHours;
        $totalUndertimeMinutes = $totalUndertimeMinutes % 60;

        // Set total undertime in the first section
        $sheet->setCellValue('G46', $totalUndertimeHours);
        $sheet->setCellValue('H46', $totalUndertimeMinutes);

        // Set total undertime in the duplicated section
        $sheet->setCellValue('P46', $totalUndertimeHours);
        $sheet->setCellValue('Q46', $totalUndertimeMinutes);

        // Format totals to be centered and bold
        foreach (['G46', 'H46', 'P46', 'Q46'] as $cell) {
            $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }



        //I CERTIFY 1
        $sheet->setCellValue('B48', '       I CERTIFY on my honor that the above is a true and correct');
        $sheet->getStyle('B48')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B48')->getFont()->setItalic(true)->setSize(9);
        $sheet->setCellValue('B49', 'report of the hours of work performed, record of which was made');
        $sheet->getStyle('B49')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B49')->getFont()->setItalic(true)->setSize(9);
        $sheet->setCellValue('B50', 'daily at the time of arrival and departure from office.');
        $sheet->getStyle('B50')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B50')->getFont()->setItalic(true)->setSize(9);
        $sheet->mergeCells('C52:H52');
        $sheet->setCellValue('C52', mb_strtoupper($user->name));
        $sheet->getStyle('C52')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C52')->getFont()->setBold(true)->setSize('11');
        $sheet->mergeCells('C53:H53');
        $sheet->setCellValue('C53', '(Signature of official or employee)');
        $sheet->getStyle('C53')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C53')->getFont()->setItalic(true)->setSize('9');

        //I CERTIFY 2
        $sheet->setCellValue('K48', '       I CERTIFY on my honor that the above is a true and correct');
        $sheet->getStyle('K48')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('K48')->getFont()->setItalic(true)->setSize(9);
        $sheet->setCellValue('K49', 'report of the hours of work performed, record of which was made');
        $sheet->getStyle('K49')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('K49')->getFont()->setItalic(true)->setSize(9);
        $sheet->setCellValue('K50', 'daily at the time of arrival and departure from office.');
        $sheet->getStyle('K50')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('K50')->getFont()->setItalic(true)->setSize(9);
        $sheet->mergeCells('L52:Q52');
        $sheet->setCellValue('L52', mb_strtoupper($user->name));
        $sheet->getStyle('L52')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L52')->getFont()->setBold(true)->setSize('11');
        $sheet->mergeCells('L53:Q53');
        $sheet->setCellValue('L53', '(Signature of official or employee)');
        $sheet->getStyle('L53')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L53')->getFont()->setItalic(true)->setSize('9');



        // Verified as 1
        $sheet->setCellValue('B54', 'Verified as to Correctness');
        $sheet->getStyle('B54')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B54')->getFont()->setItalic(true)->setSize(9);
        $sheet->mergeCells('C56:H56');
        $sheet->setCellValue('C56', mb_strtoupper($user->immediate_supervisor));
        $sheet->getStyle('C56')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C56')->getFont()->setBold(true)->setSize('11');
        $sheet->mergeCells('C57:H57');
        $sheet->setCellValue('C57', 'Immediate Supervisor');
        $sheet->getStyle('C57')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C57')->getFont()->setItalic(true)->setSize('9');

        //Verified as 2
        $sheet->setCellValue('K54', 'Verified as to Correctness');
        $sheet->getStyle('K54')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('K54')->getFont()->setItalic(true)->setSize(9);
        $sheet->mergeCells('L56:Q56');
        $sheet->setCellValue('L56', mb_strtoupper($user->immediate_supervisor));
        $sheet->getStyle('L56')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L56')->getFont()->setBold(true)->setSize('11');
        $sheet->mergeCells('L57:Q57');
        $sheet->setCellValue('L57', 'Immediate Supervisor');
        $sheet->getStyle('L57')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L57')->getFont()->setItalic(true)->setSize('9');


        //borders
        $sheet->getStyle('E4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B5:H5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DASHED,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('E8:F8')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('H8')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('G9:H9')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('G10:H10')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A12:H12')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('H12')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('C12:H12')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A13:H13')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B12:B44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('C13:C44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('D12:D44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('E13:E44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('F12:F46')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('G13:G46')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('H13:H44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A46:H46')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A47:H47')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('D46:H46')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('C52:H52')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('C56:H56')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);



        //borders 2
        $sheet->getStyle('N4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('K5:Q5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DASHED,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('N8:O8')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('Q8')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('P9:Q9')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('P10:Q10')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J12:Q12')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('Q12')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('L12:Q12')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J13:Q13')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('K12:K44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('L13:L44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('M12:M44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('N13:N44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('O12:O46')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('P13:P46')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('Q13:Q44')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J46:Q46')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J47:Q47')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('M46:Q46')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('L52:Q52')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('L56:Q56')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Generate Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'DTR.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}

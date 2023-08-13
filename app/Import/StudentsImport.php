<?php

namespace App\Import;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements ToCollection, WithHeadingRow
{

    private $departmentId;

    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $student = Student::create([
                'departmentId' => $this->departmentId,
                'famMidName' => $row['Họ và lót'],
                'name' => $row['Tên'],
                'gender' => $row['Giới tính'],
                'birthdate' => Date::excelToDateTimeObject($row['Ngày sinh']),
                'email' => $row['Email'],
                'phone' => $row['Điện thoại'],
                'enrollmentDate' => Date::excelToDateTimeObject($row['Ngày nhập học']),
            ]);
            $student->code = '2' . str_pad($student->id, 7, '0', STR_PAD_LEFT);
            $student->password = Hash::make('tksv' . substr($student->code, -4));
            $student->save();
        }
    }
}

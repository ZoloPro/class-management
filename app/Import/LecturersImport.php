<?php

namespace App\Import;

use App\Models\Lecturer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Lecturer::create([
                'famMidName' => $row['Họ và lót'],
                'name' => $row['Tên'],
                'gender' => $row['Giới tính'],
                'birthdate' => date("Y-m-d", strtotime($row['Ngày sinh'])),
                'email' => $row['Email'],
                'phone' => $row['Điện thoại'],
                'onboardingDate' => date("Y-m-d", strtotime($row['Ngày vào làm'])),
            ]);
        }
    }
}

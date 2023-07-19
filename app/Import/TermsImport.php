<?php

namespace App\Import;

use App\Models\Term;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TermsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Term::create([
                'termName' => $row['Tên học phần'],
                'credit' => $row['Số tín chỉ'],
            ]);
        }
    }
}

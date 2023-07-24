<?php

use Maatwebsite\Excel\Concerns\FromCollection;

class GradeFormExport implements FromCollection
{

    protected $classroomId;

    public function __construct($classroomId)
    {
        $this->classroomId = $classroomId;
    }

    public function collection()
    {
        $classroom = Classroom::find($this->classroomId);

        return Invoice::all();
    }
}

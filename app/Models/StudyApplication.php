<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyApplication extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToCompany;

    protected $fillable = ['company_id', 'study_program_id', 'applicant_name', 'applicant_email', 'applicant_phone', 'previous_education', 'preferred_intake', 'document_path', 'status'];

    public function program()
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }
}

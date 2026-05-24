<?php

namespace App\Classes;

class CurrentCompany
{
    public static function id(): ?int
    {
        $companyProfileId = session('company_profile.id');

        return $companyProfileId ? (int) $companyProfileId : null;
    }
}
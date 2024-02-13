<?php

namespace App\Http\Controllers\api;
use App\Models\Age;
use App\Models\Beard;
use App\Models\Country;
use App\Models\EducationalLevel;
use App\Models\Employment;
use App\Models\FamilySituation;
use App\Models\FinancialStatus;
use App\Models\HealthStatus;
use App\Models\Height;
use App\Models\Hijab;
use App\Models\MonthlyIncome;
use App\Models\Physique;
use App\Models\Prayer;
use App\Models\Religiosity;
use App\Models\SkinColour;
use App\Models\TypeMarriage;
use App\Models\Weight;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\api\AgeResource;
use App\Http\Resources\api\CountryResource;
use App\Traits\GeneralTrait;

class StandardController extends Controller
{
    use GeneralTrait;

    public function standard()
    {
        $ages = Age::all();
        $beards = Beard::all();
        $countries = Country::all();
        $educationalLevels = EducationalLevel::all();
        $employments = Employment::all();
        $familySituations = FamilySituation::all();
        $financialStatuse = FinancialStatus::all();
        $halthStatuses = HealthStatus::all();
        $heights = Height::all();
        $hijabs = Hijab::all();
        $monthlyIncomes = MonthlyIncome::all();
        $physiques = Physique::all();
        $prayers = Prayer::all();
        $religiosities = Religiosity::all();
        $skinColours = SkinColour::all();
        $typeMarriages = TypeMarriage::all();
        $weights = Weight::all();

        $ages_data = AgeResource::collection($ages);
        $beards_data = AgeResource::collection($beards);
        $countries_data = CountryResource::collection($countries);
        $educationalLevels_data = AgeResource::collection($educationalLevels);
        $employments_data = AgeResource::collection($employments);
        $familySituations_data = AgeResource::collection($familySituations);
        $financialStatuse_data = AgeResource::collection($financialStatuse);
        $halthStatuses_data = AgeResource::collection($halthStatuses);
        $heights_data = AgeResource::collection($heights);
        $hijabs_data = AgeResource::collection($hijabs);
        $monthlyIncomes_data = CountryResource::collection($monthlyIncomes);
        $physiques_data = AgeResource::collection($physiques);
        $prayers_data = AgeResource::collection($prayers);
        $religiosities_data = AgeResource::collection($religiosities);
        $skinColours_data = AgeResource::collection($skinColours);
        $typeMarriages_data = AgeResource::collection($typeMarriages);
        $weights_data = AgeResource::collection($weights);

        $data = [
                    'ages_data' => $ages_data,
                    'beards_data' => $beards_data,
                    'countries_data' => $countries_data,
                    'educationalLevels_data' => $educationalLevels_data,
                    'employments_data' => $employments_data,
                    'familySituations_data' => $familySituations_data,
                    'financialStatuse_data' => $financialStatuse_data,
                    'halthStatuses_data' => $halthStatuses_data,
                    'heights_data' => $heights_data,
                    'hijabs_data' => $hijabs_data,
                    'monthlyIncomes_data' => $monthlyIncomes_data,
                    'physiques_data' => $physiques_data,
                    'prayers_data' => $prayers_data,
                    'religiosities_data' => $religiosities_data,
                    'skinColours_data' => $skinColours_data,
                    'typeMarriages_data' => $typeMarriages_data,
                    'weights_data' => $weights_data,
                    
                ];
        return $this->returnData('data',$data);
    }
}

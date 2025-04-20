<?php

namespace App\Controller;
use App\Entity\Land;
use App\Service\AdviceService;
use App\Repository\LandRepository;
use App\Repository\WideRangeAdviceRepository;
use App\Service\CurrentWeatherService;
use App\Service\AdviceGenerationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/wide-range-advice')]
class WideRangeController extends AbstractController
{

    private CurrentWeatherService $CurrAdvice;
    private AdviceGenerationService $GenerateAdvice;
    private AdviceService $AdviceService;
    private WideRangeAdviceRepository $WideRangeRepo;

    public function __construct(CurrentWeatherService $CurrAdvice , AdviceGenerationService $GenerateAdvice , AdviceService $AdviceService,WideRangeAdviceRepository $WideRangeRepo)
    {
        $this->CurrAdvice = $CurrAdvice;
        $this->GenerateAdvice = $GenerateAdvice;
        $this->AdviceService = $AdviceService;
        $this->WideRangeRepo = $WideRangeRepo;
    }


    #[Route('/check-lands', methods: ['POST'])]
    public function checkWeather(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $geometry = $data['geometry'];
        $description = $data['description'];
        $affectedArea = $data['affectedArea'];

        //generate advice
        $advice = $this->GenerateAdvice->GenerateWideRangeAdvice($description);

        //get data from cache
        $weatherData = $this->CurrAdvice->getWeatherAllCache();

        $polygon = $this->convertGeoJSONToPolygon($geometry);

        //loop through users' lands to check
        $usersInArea = [];
        
        foreach ($weatherData['Users'] as $user) {
            foreach ($user['Farms'] as $farm) {
                foreach ($farm['Lands'] as $land) {
                    
                    // Get lat and long of land
                    $latitude = $land['CenterX'];
                    $longitude = $land['CenterY'];

                    // Check if the land's center is inside the drawn polygon
                    if ($this->isPointInPolygon($latitude, $longitude, $polygon)) {
                        
                        $this->AdviceService->InsertWideRangeAdvice($user['UserId'] ,$land['LandId'] , $advice , $description , $affectedArea);
                        
                        $usersInArea[] = [
                            'UserName' => $user['Name'],
                            'UserId' => $user['UserId'],                            
                            'FarmId' => $farm['FarmId'],
                            'LandId' => $land['LandId'],
                            'Latitude' => $latitude,
                            'Longitude' => $longitude,
                        ];
                    }
                }
            }
        }

        return new JsonResponse([
            'advice' => $advice,
            'usersInArea' => $usersInArea
        ]);
    }


    #[Route('/get-advices', methods: ['GET'])]
    public function GetAdvice(): JsonResponse{
        $advices = $this->WideRangeRepo->findAll();

        return $this->json($advices);
    }


    private function convertGeoJSONToPolygon($geometry): array
    {
        return $geometry['coordinates'][0];
    }

    private function isPointInPolygon(float $lat, float $lon, array $polygon): bool
    {
        $n = count($polygon);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i][1];
            $yi = $polygon[$i][0];
            $xj = $polygon[$j][1];
            $yj = $polygon[$j][0];

            $intersect = (($yi > $lat) != ($yj > $lat)) &&
                ($lon < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

}

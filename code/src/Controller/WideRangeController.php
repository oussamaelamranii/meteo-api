<?php

namespace App\Controller;
use App\Entity\Land;
use App\Repository\LandRepository;
use App\Service\AdviceGenerationService;
use App\Service\CurrentWeatherService;
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

    public function __construct(CurrentWeatherService $CurrAdvice , AdviceGenerationService $GenerateAdvice)
    {
        $this->CurrAdvice = $CurrAdvice;
        $this->GenerateAdvice = $GenerateAdvice;
    }


    #[Route('/check-lands', methods: ['POST'])]
    public function checkWeather(Request $request, SerializerInterface $serializer): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $geometry = $data['geometry'];
        $description = $data['description'];

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
                        $usersInArea[] = [
                            'UserName' => $user['Name'],
                            'UserId' => $user['UserId'],                            
                            'FarmName' => $farm['FarmId'],
                            'LandName' => $land['LandId'],
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

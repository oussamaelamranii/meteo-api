<?php

namespace App\Controller;
use App\Entity\Land;
use App\Service\AdviceService;
use App\Repository\LandRepository;
use App\Service\CurrentWeatherService;
use App\Service\AdviceGenerationService;
use App\Service\SendNotificationService;
use App\Repository\WideRangeAdviceRepository;
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
    private SendNotificationService $notif;

    public function __construct(CurrentWeatherService $CurrAdvice , AdviceGenerationService $GenerateAdvice , 
        AdviceService $AdviceService,WideRangeAdviceRepository $WideRangeRepo , SendNotificationService $notif )
    {
        $this->CurrAdvice = $CurrAdvice;
        $this->GenerateAdvice = $GenerateAdvice;
        $this->AdviceService = $AdviceService;
        $this->WideRangeRepo = $WideRangeRepo;
        $this->notif = $notif;
    }


    #[Route('/check-lands', methods: ['POST'])]
    public function checkWeather(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $geometry = $data['geometry'];
        $description = $data['description'];
        $affectedArea = 'Oujda, Angad';

        //generate advice
        $advice = $this->GenerateAdvice->GenerateWideRangeAdvice($description);

        $this->notif->sendEmail($advice , $description);
        // $this->notif->sendSms($advice , $description);

        //get data from cache
        $weatherData = $this->CurrAdvice->getWeatherAllCache();

        $polygon = $this->convertGeoJSONToPolygon($geometry);

        //loop through users' lands to check
        $usersInArea = [];
        
        foreach ($weatherData['users'] as $user) {
            foreach ($user['farms'] as $farm) {
                foreach ($farm['lands'] as $land) {
                    
                    // Get lat and long of land
                    $latitude = $land['centerY'];
                    $longitude = $land['centerX'];

                    // Check if the land's center is inside the drawn polygon
                    if ($this->isPointInPolygon($latitude, $longitude, $polygon)) {
                        sleep(30);
                        $this->AdviceService->InsertWideRangeAdvice($user['userId'] , $land['landId'] , $advice , $description , $affectedArea);
                        
                        $usersInArea[] = [
                            'UserName' => $user['name'],
                            'UserId' => $user['userId'],                            
                            'FarmId' => $farm['farmId'],
                            'LandId' => $land['landId'],
                            'Latitude' => $latitude,
                            'Longitude' => $longitude,
                        ];
                    }
                }
            }
        }

        return new JsonResponse([
            'advice' => $advice,
            'AffectedUsers'=>$usersInArea
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

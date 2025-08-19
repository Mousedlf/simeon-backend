<?php

namespace App\Service;

use App\Entity\DayOfTrip;
use App\Entity\Image;
use App\Entity\TripActivity;
use App\Repository\ActivityCategoryRepository;
use App\Repository\DayOfTripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ActivityService
{
    public function __construct(
        public EntityManagerInterface $manager,
        public SerializerInterface $serializer,
        public ActivityCategoryRepository $activityCategoryRepository,
        public dayOfTripRepository $dayOfTripRepository,
        public GooglePlacesService $googlePlacesService,
    ){}

    /**
     * Add activity to a day.
     * @param DayOfTrip $dayOfTrip
     * @param Request $request
     * @return array
     */
    public function addActivityToTrip(Request $request) : array
    {
        $activity = $this->serializer->deserialize($request->getContent(), TripActivity::class, 'json');
        $data = $request->toArray();

        $category = $this->activityCategoryRepository->findOneBy(['id' => $data['category']]);
        $dayOfTrip = $this->dayOfTripRepository->findOneBy(['id' => $data['dayOfTrip']]);

        if (!$category) {
            throw new \Exception('Activity category not found with ID ' . $data['category']);
        }
        if (!$dayOfTrip) {
            throw new \Exception('Day of trip not found with ID ' . $data['dayOfTrip']);
        }

        $activity->setCategory($category);
        $activity->addDay($dayOfTrip);

        $activitiesCount = count($dayOfTrip->getActivities());
        $activity->setSequence($activitiesCount + 1);

        if (!empty($data['name'])) {
            $placeId = $this->googlePlacesService->findPlaceId($data['name']);
            if ($placeId) {
                $details = $this->googlePlacesService->getPlaceDetails($placeId);
                if (!empty($details['photos'][0]['photo_reference'])) {
                    $photoUrl = $this->googlePlacesService->getPhotoUrl($details['photos'][0]['photo_reference']);

                    $image = new Image();
                    $image->setGoogleImageUrl($photoUrl);
                    $image->setUpdatedAt(new \DateTimeImmutable());
                    $image->setTripActivity($activity);
                    $this->manager->persist($image);
                    $activity->setImage($image);
                }
            }
        }

        $this->manager->persist($activity);
        $this->manager->flush();

        return [
            'activity' => $activity,
            'message'=>'Activity created',
        ];
    }

    /**
     * Edit activity.
     * @param DayOfTrip $dayOfTrip
     * @param Request $request
     * @param TripActivity $activity
     * @return array
     */
    public function editActivityOfTrip(DayOfTrip $dayOfTrip, Request $request, TripActivity $activity) : array
    {
        $data = $request->toArray();

        $activity->setLatitude($data['latitude']);
        $activity->setLongitude($data['longitude']);
        $activity->setName($data['name']);
        $activity->setAddress($data['address']);
        $activity->setCategory($this->activityCategoryRepository->findOneBy(['id' => $data['category']]));
        $activity->addDay($dayOfTrip);

        $this->manager->persist($activity);
        $this->manager->flush();

        return [
            'activity' => $activity,
            'message'=>'Activity updated',
        ];
    }

    /**
     * Delete activity.
     * @param TripActivity $tripActivity
     */
    public function deleteActivity(TripActivity $tripActivity) : void
    {
        $this->manager->remove($tripActivity);
        $this->manager->flush();
    }

    /**
     * Reorder activities of a day.
     * @param DayOfTrip $dayOfTrip
     * @param Request $request
     * @return string
     */
    public function reorderActivities(DayOfTrip $dayOfTrip, Request $request): string
    {
        $data = $request->toArray();

        if (!isset($data['order'])) {
            return 'No order provided';
        }

        $existingActivitiesMap = [];
        foreach ($dayOfTrip->getActivities() as $activity) {
            $existingActivitiesMap[$activity->getId()] = $activity;
        }

        $newSequence = 0;
        foreach ($data['order'] as $activityId) {

            if (isset($existingActivitiesMap[$activityId])) {
                $activity = $existingActivitiesMap[$activityId];
                $activity->setSequence($newSequence + 1);
                $newSequence++;

                $this->manager->persist($activity);
            } else {
                return "The activity with the id {$activityId} doesn't exist";
            }
        }

        $this->manager->flush();
        return "Order successfully reordered.";
    }

}
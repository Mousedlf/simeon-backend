<?php

namespace App\Service;

use App\Entity\DayOfTrip;
use App\Entity\Trip;
use App\Entity\TripActivity;
use App\Repository\ActivityCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ActivityService
{
    public function __construct(
        public EntityManagerInterface $manager,
        public SerializerInterface $serializer,
        public ActivityCategoryRepository $activityCategoryRepository,
    ){}

    /**
     * Add activity to a day.
     * @param DayOfTrip $dayOfTrip
     * @param Request $request
     * @return array
     */
    public function addActivityToTrip(DayOfTrip $dayOfTrip, Request $request) : array
    {
        $activity = $this->serializer->deserialize($request->getContent(), TripActivity::class, 'json');
        $data = $request->toArray();

        $activity->setCategory($this->activityCategoryRepository->findOneBy(['id' => $data['category']]));
        $activity->addDay($dayOfTrip);

        $activitiesCount= count($dayOfTrip->getActivities());
        $activity->setSequence($activitiesCount + 1);

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
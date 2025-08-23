<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentFile;
use App\Entity\Trip;
use App\Entity\TripParticipant;
use App\Repository\ActivityCategoryRepository;
use App\Repository\DayOfTripRepository;
use App\Repository\TripActivityRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class DocumentService
{
    public function __construct(
        public EntityManagerInterface $manager,
        public SerializerInterface $serializer,
        public ActivityCategoryRepository $activityCategoryRepository,
        public dayOfTripRepository $dayOfTripRepository,
        public TripActivityRepository $tripActivityRepository,
    )
    {
    }

    /**
     * Add document to a trip.
     * @param Trip $trip
     * @param TripParticipant $participant
     * @param $uploadedFile
     * @param $jsonData
     * @return array
     * @throws Exception
     */
    public function addDocumentToTrip(Trip $trip, TripParticipant $participant, $uploadedFile, $jsonData): array
    {
        $document = $this->serializer->deserialize($jsonData, Document::class, 'json');
        $document->setTrip($trip);
        $document->setAddedBy($participant);
        $document->setAddedAt(new DateTimeImmutable());

        if (!empty($jsonData['category'])) {
            $category = $this->activityCategoryRepository->findOneBy(['id' => $jsonData['category']]);
            if (!$category) {
                throw new Exception('Activity category not found with ID ' . $jsonData['category']);
            }
            $document->setCategory($category);
        }

        if (!empty($jsonData['tripActivity'])) {
            $tripActivity = $this->tripActivityRepository->findOneBy(['id' => $jsonData['tripActivity']]);
            if (!$tripActivity) {
                throw new Exception('Trip activity not found with ID ' . $jsonData['tripActivity']);
            }
            $document->setTripActivity($tripActivity);
        }

        $file = new DocumentFile();
        $file->setFile($uploadedFile);
        $file->setUpdatedAt(new DateTimeImmutable());

        $this->manager->persist($file);
        $document->setFile($file);

        $this->manager->persist($document);
        $this->manager->flush();

        return ['message' => 'Document added to trip', 'document' => $document,];
    }

    /**
     * Edit document of a trip.
     * @param Document $document
     * @param TripParticipant $participant
     * @param UploadedFile|null $uploadedFile
     * @param $jsonData
     * @return array
     * @throws Exception
     */
    public function editDocumentOfATrip(Document $document, TripParticipant $participant, ?UploadedFile $uploadedFile, $jsonData): array
    {
        $data = json_decode($jsonData);
        $document->setName($data->name);
        $document->setDescription($data->description);
        $document->setAddedBy($participant);
        $document->setAddedAt(new DateTimeImmutable());

        if (!empty($data->category)) {
            $category = $this->activityCategoryRepository->findOneBy(['id' => $data->category]);
            if (!$category) {
                throw new Exception('Activity category not found with ID ' . $data->category);
            }
            $document->setCategory($category);
        }

        if (!empty($data->tripActivity)) {
            $tripActivity = $this->tripActivityRepository->findOneBy(['id' => $data->tripActivity]);
            if (!$tripActivity) {
                throw new Exception('Trip activity not found with ID ' . $data->tripActivity);
            }
            $document->setTripActivity($tripActivity);
        }

        if(!empty($uploadedFile)) {
            $file = new DocumentFile();
            $file->setFile($uploadedFile);
            $file->setUpdatedAt(new DateTimeImmutable());

            $this->manager->persist($file);
            $document->setFile($file);
        }

        $this->manager->persist($document);
        $this->manager->flush();

        return ['message' => 'Document edited', 'document' => $document,];
    }


    /**
     * Add document to a user.
     * @param $user
     * @param $uploadedFile
     * @param $jsonData
     * @return array
     */
    public function addDocumentToUser($user, $uploadedFile, $jsonData): array
    {
        $document = $this->serializer->deserialize($jsonData, Document::class, 'json');
        $document->setOfUser($user);
        $document->setAddedAt(new DateTimeImmutable());

        $file = new DocumentFile();
        $file->setFile($uploadedFile);
        $file->setUpdatedAt(new DateTimeImmutable());

        $this->manager->persist($file);
        $document->setFile($file);

        $this->manager->persist($document);
        $this->manager->flush();

        return ['message' => 'Document added to user', 'document' => $document,];
    }

    /**
     * Delete a document.
     * @param Document $document
     * @return string
     */
    public function deleteDocument(Document $document):string
    {
        $this->manager->remove($document);
        $this->manager->flush();

        return "Document successfully deleted";
    }

}
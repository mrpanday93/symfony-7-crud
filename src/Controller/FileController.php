<?php

namespace App\Controller;
use App\Entity\File;
use App\Entity\FileHeader;
use App\Entity\FileValues;
use App\Repository\FileRepository;
use App\Repository\FileHeaderRepository;
use App\Repository\FileValuesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\UserRepository;

#[Route('/api', name: 'api_')]
class FileController extends AbstractController
{   
    private $fileRepository;
    private $fileHeaderRepository;
    private $fileValuesRepository;
    private $userRepository;
    private $tokenStorageInterface;
    private $jwtManager;
    private $decodedJwtToken;
	private $user;

	public function __construct(FileRepository $fileRepository, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, FileHeaderRepository $fileHeaderRepository, FileValuesRepository $fileValuesRepository)
	{
		$this->fileRepository = $fileRepository;
		$this->fileHeaderRepository = $fileHeaderRepository;
		$this->fileValuesRepository = $fileValuesRepository;
		$this->userRepository = $userRepository;
		$this->jwtManager = $jwtManager;
    	$this->tokenStorageInterface = $tokenStorageInterface;
		$this->decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
		$this->user = $this->userRepository->findOneByField('username',$this->decodedJwtToken['username']);
		
	}

	#[Route('/files', name: 'create_file', methods: ['POST'])]
	public function createFile(Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		
		if (!$this->user) {
			return $this->json(['message'=> 'Not Authorized'], 401);
		}

		$entityManager->beginTransaction();

		try {
			$file = new File();
			$file->setTitle($data['title']);
			
			$file->setUserId($this->user->getId());
	
			$entityManager->persist($file);
		
			$entityManager->flush();
	
			$fileId = $file->getId();
			$response['file'] = $file;
			$i=0;
			foreach ($data['data'] as $header => $records) {
				$fileHeader = new FileHeader();
				$fileHeader->setTitle($header);
				$fileHeader->setFileId($fileId);
				$fileHeader->setColumnIndex($i);
				$entityManager->persist($fileHeader);
				$entityManager->flush();
	
				$headerId = $fileHeader->getId();
	
				
				$response['header'][$header] = $records;
	
				foreach ($records as $record) {
					$fileValues = new FileValues();
					$fileValues->setHeaderId($headerId);
					$fileValues->setValue($record['value']);
					$fileValues->setRowNumber($record['row']);
					$entityManager->persist($fileValues);
					if (($record['row'] % count($record)) === 0) {
						$entityManager->flush();
						$entityManager->clear();
					}
				}
				$i++;
			}
	
	
			$entityManager->commit();
			
		} catch (\Exception $e) {
			$entityManager->rollback();
            throw $e;
		}

		return $this->json(['file'=>$response], 200);
	}

	#[Route('/files', name: 'fetch_files', methods: ['GET'])]
	public function fetchAll(): JsonResponse
	{
		$files = $this->fileRepository->findBy([],['createdAt' => 'DESC']);
		
		return $this->json($files, 200);
	}
	
	#[Route('/files/{id}', name: 'fetch_file', methods: ['GET'])]
	public function fetchById(int $id): JsonResponse
	{
		$files = $this->fileRepository->findById($id);

		$headers = $this->fileHeaderRepository->findBy(['file_id' => $id],   
		['column_index' => 'ASC']);

		foreach ($headers as $header) {
			$values[$header->getTitle()] = $this->fileValuesRepository->findBy(['header_id' => $header->getId()], 
			['row_number' => 'ASC']);
		}

		$finalData = [];

		foreach ($values as $key => $value) {
			foreach ($value as $index=>$record) {
                $finalData[$index][$key] = $record->getValue();
            }
		}
		
		return $this->json($finalData, 200);
	}

	#[Route('/files/{id}', name: 'update_file', methods: ['PUT'])]
	public function updateFile(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		
		$file = $this->fileRepository->find($id);
		
		if ($data['type'] === 'archived') {
			$file->setArchived($data['value']);
		}

		if ($data['type'] === 'stared') {
			$file->setStared($data['value']);
		}

		$entityManager->persist($file);
		$entityManager->flush();

		return $this->json($file, 200);
	}
}

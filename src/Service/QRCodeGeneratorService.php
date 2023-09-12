<?php

namespace App\Service;

use App\Repository\EmployeeRepository;
use Endroid\QrCode\Builder\BuilderInterface;

/**
 * Service for QR Code generation.
 * Author: Jerico Lua (+help of Endroid/QrCode bundle)
 */
class QRCodeGeneratorService
{
    private BuilderInterface $builderInterface;
    private EmployeeRepository $employeeRepository;

    /**
     * @param BuilderInterface $builderInterface
     * @param EmployeeRepository $employeeRepository
     */
    public function __construct(BuilderInterface $builderInterface, EmployeeRepository $employeeRepository)
    {
        $this->builderInterface = $builderInterface;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Generate QR Code with the use of Endroid QR Code bundle. Add URL as data to the QR Code.
     * @param $employee_id
     * @param $slug /* combined first and last name of employee
     * @param $request
     * @return string
     */
    public function generateQRCode($employee_id, $slug, $request): string
    {
        $url = $request;
        $employee = $this->employeeRepository->findOneBy(['id' => $employee_id, 'slug' => $slug]);


        $qr = $this->builderInterface
            //Updated to match with the new route of the business card
            ->data($url . 'visitenkarte/' . $employee_id . '/' . $slug)
            ->size(220)
            ->build();

        if(!is_dir('images/qr-code')){
            mkdir('images/qr-code/', 0777, true); //Create folders recursively.
        }

        //Do not create QR Code files if employee is empty.
        //If it isn't empty save the file inside the images/qr-code folder.
        if($employee != null){
            $qr->saveToFile('images/qr-code/' . $employee_id . '-' . $slug . '.png');
        }

        return $qr->getDataUri();
    }
}
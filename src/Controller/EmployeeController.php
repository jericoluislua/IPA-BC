<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\EmployeeRole;
use App\Entity\SocialMediaEmployee;
use App\Form\EmployeeFormType;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Repository\EmployeeRoleRepository;
use App\Repository\SocialMediaEmployeeRepository;
use App\Repository\SocialMediaRepository;
use App\Service\QRCodeGeneratorService;
use App\Service\VCardGeneratorService;
use Doctrine\Persistence\ManagerRegistry;
use JeroenDesloovere\VCard\VCardException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This is where all the interaction with the Employee entity will be.
 * Author: Jerico Lua (+help of Make bundle)
 */
class EmployeeController extends AbstractController
{
    /**
     * Shows all employees. Name and company role information of the employee are shown.
     * @param EmployeeRepository $employeeRepository
     * @param EmployeeRoleRepository $employeeRoleRepository
     * @return Response
     */
    #[Route('/administrator/mitarbeitende/liste', name: 'employee_list')]
    public function list(EmployeeRepository $employeeRepository, EmployeeRoleRepository $employeeRoleRepository): Response
    {
        //Only give access to logged in users with role admin. Send out a flash message if someone tries to access page without role admin. Redirect to loginpage.
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Kein Zugang. Sie müssen eingeloggt sein.');
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $allEmployees = $employeeRepository->findAll();
        $allEmployeeRoles = $employeeRoleRepository->findAll();

        return $this->render('employee/list.html.twig', [
            'allEmployees' => $allEmployees,
            'allEmployeeRoles' => $allEmployeeRoles,
        ]);
    }

    /**
     * Creates an employee with the help of EmployeeFormType and ManagerRegistry (doctrine).
     * @param Request $request
     * @param SocialMediaRepository $socialMediaRepository
     * @param CompanyRepository $companyRepository
     * @param ManagerRegistry $doctrine
     * @param QRCodeGeneratorService $qrCodeGeneratorService
     * @param VCardGeneratorService $vCardGeneratorService
     * @return Response
     * @throws VCardException
     */
    #[Route('/administrator/mitarbeitende/erstellen', name: 'employee_create')]
    public function createEmployee(Request $request, SocialMediaRepository $socialMediaRepository, CompanyRepository $companyRepository, ManagerRegistry $doctrine, QRCodeGeneratorService $qrCodeGeneratorService, VCardGeneratorService $vCardGeneratorService): Response
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/'; //Get the current URL
        //Only give access to logged in users with role admin. Send out a flash message if someone tries to access page without role admin. Redirect to loginpage.
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Kein Zugang. Sie müssen eingeloggt sein.');
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $socialMediaPlatforms = $socialMediaRepository->findAll();
        $company = $companyRepository->findOneBy(['name' => 'Artd Webdesign GmbH']);

        $form = $this->createForm(EmployeeFormType::class);
        $form->handleRequest($request);

        //Check if form has been submitted and if it is valid.
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $formDataMobile = $form->get('mobile')->getData();
            $formDataTelephone = $form->get('telephone')->getData();

            //Save employee data
            $employee = new Employee();
            $this->setEmployee($employee, $form->get('first_name')->getData(), $form->get('last_name')->getData(), $form->get('email')->getData(), $this->removePrefixZero($formDataMobile), $this->removePrefixZero($formDataTelephone));

            $entityManager->persist($employee);

            //Go through all existing platforms
            foreach($socialMediaPlatforms as $socialMediaPlatform){
                //Save social media profiles
                $socialMediaProfile = new SocialMediaEmployee();
                $socialMediaProfile->setUsername(''); //Default value is white-space but not NULL.
                $socialMediaProfile->setEmployeeFkid($employee);
                $socialMediaProfile->setSocialMediaFkid($socialMediaPlatform);
                //If input field value is not only white space, set the social media profile username to this value.
                if($form->get(strtolower($socialMediaPlatform->getPlatform()))->getData() != ''){
                    $socialMediaProfile->setUsername($form->get(strtolower($socialMediaPlatform->getPlatform()))->getData());
                }
                $entityManager->persist($socialMediaProfile);
            }

            //Save employee role data
            $employeeRole = new EmployeeRole();
            $employeeRole->setEmployeeFkid($employee);
            $employeeRole->setRole($form->get('role')->getData());
            $employeeRole->setCompanyFkid($company);
            $entityManager->persist($employeeRole);
            $entityManager->flush();

            $qrCodeGeneratorService->generateQRCode($employee->getId(), $employee->getSlug(), $url);
            $vCardGeneratorService->generateVCard($employee->getId(), $employee->getSlug());
            //A successful notification flash, that the employee has been created.
            $this->addFlash(
                'success', 'Mitarbeitende ' . $employee->getFirstName() . ' ' . $employee->getLastName() . ' wurde erstellt');
            return $this->redirectToRoute('employee_list');
        }

        //View: Render the html twig file
        return $this->render('employee/create.html.twig', [
            'form' => $form->createView(),
            'socialMediaPlatforms' => $socialMediaPlatforms
        ]);
    }

    /**
     * Updates the employee data with the help of the EmployeeFormType and ManagerRegistry (doctrine).
     * @param $employee_id
     * @param Request $request
     * @param EmployeeRoleRepository $employeeRoleRepository
     * @param SocialMediaEmployeeRepository $socialMediaEmployeeRepository
     * @param ManagerRegistry $doctrine
     * @param QRCodeGeneratorService $qrCodeGeneratorService
     * @param VCardGeneratorService $vCardGeneratorService
     * @return Response
     * @throws VCardException
     */
    #[Route('/administrator/mitarbeitende/{employee_id?}/bearbeiten', name: 'employee_edit')]
    public function editEmployee($employee_id, Request $request, EmployeeRoleRepository $employeeRoleRepository, SocialMediaEmployeeRepository $socialMediaEmployeeRepository, ManagerRegistry $doctrine, QRCodeGeneratorService $qrCodeGeneratorService, VCardGeneratorService $vCardGeneratorService): Response
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/'; //Get the current URL
        //Only give access to logged in users with role admin. Send out a flash message if someone tries to access page without role admin. Redirect to loginpage.
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Kein Zugang. Sie müssen eingeloggt sein.');
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $employeeRole = $employeeRoleRepository->findOneBy(['employee_fkid' => $employee_id]);
        $employee = $employeeRole->getEmployeeFkid();
        $socialMediaProfiles = $socialMediaEmployeeRepository->findBy(['employee_fkid' => $employee_id]);

        $form = $this->createForm(EmployeeFormType::class);
        $form->handleRequest($request);

        //Check if form has been submitted and if it is valid.
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $formDataMobile = $form->get('mobile')->getData();
            $formDataTelephone = $form->get('telephone')->getData();

            //Update employee data
            $this->setEmployee($employee, $form->get('first_name')->getData(), $form->get('last_name')->getData(), $form->get('email')->getData(), $this->removePrefixZero($formDataMobile), $this->removePrefixZero($formDataTelephone));

            $entityManager->persist($employee);

            //Update Social Media profiles
            foreach ($socialMediaProfiles as $socialMediaProfile){
                $socialMediaProfile->setUsername(''); //Default value is white-space but not NULL.
                if($form->get(strtolower($socialMediaProfile->getSocialMediaFkid()->getPlatform()))->getData() != null){
                    $socialMediaProfile->setUsername($form->get(strtolower($socialMediaProfile->getSocialMediaFkid()->getPlatform()))->getData());
                }
                $entityManager->persist($socialMediaProfile);
            }

            //Update employee role
            $employeeRole->setRole($form->get('role')->getData());
            $entityManager->persist($employeeRole);
            $entityManager->flush();

            $qrCodeGeneratorService->generateQRCode($employee->getId(), $employee->getSlug(), $url);
            $vCardGeneratorService->generateVCard($employee->getId(), $employee->getSlug());
            //A successful notification flash, that the employee data has been updated.
            $this->addFlash(
                'success', 'Mitarbeitendedaten von ' . $employee->getFirstName() . ' ' . $employee->getLastName() . ' wurde aktualisiert.');
            return $this->redirectToRoute('employee_list');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
            'employeeRole' => $employeeRole,
            'socialMediaProfiles' => $socialMediaProfiles,
        ]);
    }

    /**
     * Delete all employee data including the employee's vCard and QR Code.
     * The deletion was done with the help of ManagerRegistry doctrine (deletion of data in the database) and Filesystem (deletion of vCard and QR Code.
     * @param $employee_id
     * @param EmployeeRepository $employeeRepository
     * @param EmployeeRoleRepository $employeeRoleRepository
     * @param SocialMediaEmployeeRepository $socialMediaEmployeeRepository
     * @param ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    #[Route('/administrator/mitarbeitende/{employee_id?}/loeschen', name: 'employee_delete')]
    public function deleteEmployee($employee_id, EmployeeRepository $employeeRepository, EmployeeRoleRepository $employeeRoleRepository, SocialMediaEmployeeRepository $socialMediaEmployeeRepository, ManagerRegistry $doctrine): RedirectResponse
    {
        //Only give access to logged in users with role admin. Send out a flash message if someone tries to access page without role admin. Redirect to loginpage.
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Kein Zugang. Sie müssen eingeloggt sein.');
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $employeeRole = $employeeRoleRepository->findOneBy(['employee_fkid' => $employee_id]);
        $employee = $employeeRepository->find($employee_id);
        $socialMediaProfiles = $socialMediaEmployeeRepository->findBy(['employee_fkid' => $employee_id]);

        //Get needed employee data for file deletion before employee data are deleted.
        $fullVCardFilePath = 'vcards/' .  $employee_id . '-' . $employee->getSlug() . '.vcf';
        $fullQRCodeFilePath = 'images/qr-code/' .  $employee_id . '-' . $employee->getSlug() . '.png';

        //Delete vCard and QR Code of employee
        $fileSystem = new FileSystem();
        $fileSystem->remove($fullVCardFilePath);
        $fileSystem->remove($fullQRCodeFilePath);

        $entityManager = $doctrine->getManager();

        //Delete EmployeeRole
        $entityManager->remove($employeeRole);
        //Go through all social media profiles of employee and delete them.
        foreach($socialMediaProfiles as $socialMediaProfile){
            $entityManager->remove($socialMediaProfile);
        }
        //Delete Employee
        $entityManager->remove($employee);

        $entityManager->flush();



        $this->addFlash(
            'success', 'Mitarbeitende ' . $employee->getFirstName() . ' ' . $employee->getLastName() . ' wurde endgültig aus dem System gelöscht.');
        return $this->redirectToRoute('employee_list');
    }

    /**
     * Process all the data that the view html file needs.
     * @param $employee_id
     * @param $slug
     * @param EmployeeRoleRepository $employeeRoleRepository
     * @param SocialMediaEmployeeRepository $socialMediaEmployeeRepository
     * @return Response
     */
    #[Route('/visitenkarte/{employee_id?}/{slug?}', name: 'employee_businesscard')]
    public function viewBusinessCard($employee_id, $slug, EmployeeRoleRepository $employeeRoleRepository, SocialMediaEmployeeRepository $socialMediaEmployeeRepository): Response
    {

        $employee = '';
        $company = '';
        $employeeRole = $employeeRoleRepository->findOneBy(['employee_fkid' => $employee_id]);

        if($employeeRole == null){
            return $this->redirectToRoute('app_error');
        }

        $employee = $employeeRole->getEmployeeFkid();

        if($slug == null){
            //Redirect user if employee exists but only id is typed in URL.
            return $this->redirectToRoute('employee_businesscard', ['employee_id' => $employee_id, 'slug' => $employee->getSlug()]);
        }
        $company = $employeeRole->getCompanyFkid();

        $socialMediaProfiles = $socialMediaEmployeeRepository->findBy(['employee_fkid' => $employee_id]);
        return $this->render('employee/view_businesscard.html.twig', [
            'employee' => $employee,
            'employeeRole' => $employeeRole,
            'company' => $company,
            'socialMediaProfiles' => $socialMediaProfiles,
            'slug' => $slug
        ]);
    }


    private function removePrefixZero($formFieldData): string
    {
        if(!(strlen($formFieldData) == 13 && (str_starts_with($formFieldData, '0')))){
            return $formFieldData; //Return the value without changes if data isn't 13 characters long that starts with '0'.
        }
        return substr($formFieldData,1, strlen($formFieldData)-1); //Take data. Read data with an offset of 1 and stop reading 1 character earlier. Return data.
    }

    private function setEmployee($employee, $formDataFirstName, $formDataLastName, $formDataEmail, $formDataMobile, $formDataTelephone)
    {
        //Reduce code redundancy by putting the code below in a separate function (here in setEmployee())
        $employee->setFirstName($formDataFirstName);
        $employee->setLastName($formDataLastName);
        $employee->setEmail($formDataEmail);
        $employee->setMobile($formDataMobile);
        $employee->setTelephone($formDataTelephone);
        //Update after test protocol 1: Trim whitespaces in slug to not get funky errors when trying to load/download vCard or QR Code.
        //Updated after test protocol 1: made $slug lowercase to reduce lowercase redundancy (QR Code, vCard and in business card)
        //QR Code appears in development but doesn't appear in production if file is with uppercase but is called with lower case from business card.
        $employee->setSlug(strtolower(str_replace(' ', '',$formDataFirstName) . str_replace(' ', '', $formDataLastName)));
    }
}

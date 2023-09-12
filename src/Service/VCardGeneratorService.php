<?php

namespace App\Service;

use App\Repository\EmployeeRoleRepository;
use App\Repository\SocialMediaEmployeeRepository;
use JeroenDesloovere\VCard\VCard;
use JeroenDesloovere\VCard\VCardException;

/**
 * Service for vCard generation.
 * Author: Jerico Lua (+help of JeroenDesloovere/VCard bundle)
 */
class VCardGeneratorService
{
    private EmployeeRoleRepository $employeeRoleRepository;
    private SocialMediaEmployeeRepository $socialMediaEmployeeRepository;

    public function __construct(EmployeeRoleRepository $employeeRoleRepository, SocialMediaEmployeeRepository $socialMediaEmployeeRepository)
    {
        $this->employeeRoleRepository = $employeeRoleRepository;
        $this->socialMediaEmployeeRepository = $socialMediaEmployeeRepository;
    }

    /**
     * Generate the vCard file with the use of VCard bundle. Add all possible employee information in the vcard.
     * @param $employee_id
     * @param $slug
     * @return string
     * @throws VCardException
     */
    public function generateVCard($employee_id, $slug): string
    {
        $employeeRole = $this->employeeRoleRepository->findOneBy(['employee_fkid' => $employee_id]);
        $socialMediaProfiles = $this->socialMediaEmployeeRepository->findBy(['employee_fkid' => $employee_id]);
        $employee = $employeeRole->getEmployeeFKID();
        $company = $employeeRole->getCompanyFKID();
        $address = $employeeRole->getCompanyFKID()->getAddressFKID();

        $firstname = $employee->getFirstName();
        $lastname = $employee->getLastName();
        $fullStreet = $address->getStreet() . " " . $address->getHouseNumber();

        $vcard = new VCard();

        //Add personal data
        $vcard->addName($lastname, $firstname);

        //Add work data
        $vcard->addCompany($company->getName());
        $vcard->addJobtitle($employeeRole->getRole());
        $vcard->addEmail($employee->getEmail());
        //Updated after test protocol 1: added the missing international call prefix
        $vcard->addPhoneNumber('+41 ' . $employee->getMobile(), 'HOME');
        $vcard->addPhoneNumber('+41 ' . $employee->getTelephone(), 'WORK');
        $vcard->addAddress(null, null, $fullStreet, $address->getTown(), $address->getCanton(), $address->getPostalCode(), $address->getCountry());
        $vcard->addURL('https://www.artd.ch', 'WORK');
        foreach($socialMediaProfiles as $socialMediaProfile){
            //As there are no social media links in contact applications, it will be shown as 'Homepage'.
            if($socialMediaProfile->getUsername() != ''){
                $vcard->addURL($socialMediaProfile->getSocialMediaFkid()->getLink() . $socialMediaProfile->getUsername(), $socialMediaProfile->getSocialMediaFkid()->getPlatform());
            }
        }
        $filename = $employee_id . '-' . $slug;
        $vcard->setFilename($filename, true, '-');

        //Create vcards folder if it doesn't exist.
        if(!is_dir('vcards')){
            mkdir('vcards/', 0777);
        }

        //Do not create vCard files if employee is empty.
        //If it isn't empty save the file inside the vcards folder.
        if($employeeRole != null){
            $vcard->setSavePath('vcards');
            $vcard->save();
        }

        return $filename . '.' . $vcard->getFileExtension();
    }
}
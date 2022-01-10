<?php
declare(strict_types=1);
namespace App\Services\CryptoPro;


use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverter;

class CryptoPro
{

    public static function verify(string $data, string $signedData) {
   //     return true;
        try {
            $sd = new \CPSignedData();
            $sd->set_ContentEncoding(\BASE64_TO_BINARY);
            $sd->set_Content(base64_encode($data));

            $sd->VerifyCades($signedData, \CADES_BES, 1);
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getCertInfo($data, $sign): array
    {

//        "юрлицо
// subject:
//SNILS=14575361882, T=Генеральный директор, G=Ринат Рамилевич, SN=Галяутдинов, CN=\"ООО \"\"ЕТП РБ\"\"\", O=\"ООО \"\"ЕТП РБ\"\"\", STREET=\"50-Летия Октября ул, д. 11/2, оф. 316\", S=02 Республика Башкортостан, L=Уфа г, C=RU, INN=000275904346, E=galyrinat@gmail.com (E=galyrinat@gmail.com), OGRN=1150280072040
//
//issuer:"

  /*      $subjectNameETPRB = 'SNILS=14575361882, T=Генеральный директор, G=Ринат Рамилевич, SN=Галяутдинов, CN="ООО ""ЕТП РБ""", O="ООО ""ЕТП РБ""", STREET="50-Летия Октября ул, д. 11/2, оф. 316", S=02 Республика Башкортостан, L=Уфа г, C=RU, INN=000275904346, E=galyrinat@gmail.com (E=galyrinat@gmail.com), OGRN=1150280072040';
        $issuerNameETPRB = 'CN="ООО ""АСТРАЛ-М""", O="ООО ""АСТРАЛ-М""", E=ca@astralm.ru (E=ca@astralm.ru), S=77 г. Москва, L=г. Москва, C=RU, INN=007720623379, STREET=Шоссе Энтузиастов д. 56 стр.32 офис 214, OGRN=1087746806311';

        $subjectNameLushnikova = 'SNILS=13877946832, INN=027200518388, E=9876295060laa@gmail.com (E=9876295060laa@gmail.com), CN=Лушникова Анастасия Александровна, SN=Лушникова, G=Анастасия Александровна, C=RU, L=ГОР. УФА, S=02 Республика Башкортостан';
        $issuerNameLushnikova = 'CN="ООО ""КОМПАНИЯ ""ТЕНЗОР""", O="ООО ""КОМПАНИЯ ""ТЕНЗОР""", OU=Удостоверяющий центр, STREET="Московский проспект, д. 12", L=г. Ярославль, S=76 Ярославская область, C=RU, INN=007605016030, OGRN=1027600787994, E=ca_tensor@tensor.ru (E=ca_tensor@tensor.ru)';

        return [
            'thumbprint' => '',
            'email' => (new SubjectConverter($subjectNameETPRB))->toExtractEmail(),
            'inn' => (new SubjectConverter($subjectNameETPRB))->toExtractInn()
        ];*/


        try {
            $sd = new \CPSignedData();
            $sd->set_ContentEncoding(\BASE64_TO_BINARY);
            $sd->set_Content(base64_encode($data));
            $sd->VerifyCades($sign, \CADES_BES, 1);
            $signObject = $sd->get_Signers();
            $sObj = $signObject->get_Item(1);
            $cert = $sObj->get_Certificate();
            $subjectName = $cert->get_SubjectName();
            $Thumbprint = $cert->get_Thumbprint();

            return [
                'thumbprint' => $Thumbprint,
                'email' => (new SubjectConverter($subjectName))->toExtractEmail(),
                'inn' => (new SubjectConverter($subjectName))->toExtractInn()
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getCertInfoFull($data, $sign): array
    {


//        $subjectNameETPRB = 'SNILS=14575361882, T=Генеральный директор, G=Ринат Рамилевич, SN=Галяутдинов, CN="ООО ""ЕТП РБ""", O="ООО ""ЕТП РБ""", STREET="50-Летия Октября ул, д. 11/2, оф. 316", S=02 Республика Башкортостан, L=Уфа г, C=RU, INN=000275904346, E=galyrinat@gmail.com (E=galyrinat@gmail.com), OGRN=1150280072040';
//        $issuerNameETPRB = 'CN="ООО ""АСТРАЛ-М""", O="ООО ""АСТРАЛ-М""", E=ca@astralm.ru (E=ca@astralm.ru), S=77 г. Москва, L=г. Москва, C=RU, INN=007720623379, STREET=Шоссе Энтузиастов д. 56 стр.32 офис 214, OGRN=1087746806311';
//        $tumprintETPRB = '9FDC8AAFA57BA383C7659DE64EBBF9652A482C72';
//
//        $subjectNameLushnikova = 'SNILS=13877946832, INN=027200518388, E=9876295060laa@gmail.com (E=9876295060laa@gmail.com), CN=Лушникова Анастасия Александровна, SN=Лушникова, G=Анастасия Александровна, C=RU, L=ГОР. УФА, S=02 Республика Башкортостан';
//        $issuerNameLushnikova = 'CN="ООО ""КОМПАНИЯ ""ТЕНЗОР""", O="ООО ""КОМПАНИЯ ""ТЕНЗОР""", OU=Удостоверяющий центр, STREET="Московский проспект, д. 12", L=г. Ярославль, S=76 Ярославская область, C=RU, INN=007605016030, OGRN=1027600787994, E=ca_tensor@tensor.ru (E=ca_tensor@tensor.ru)';

//        return [
//            'thumbprint' => $tumprintETPRB,
//            'issuerName' => $issuerNameETPRB,
//            'subjectName' => $subjectNameETPRB,
//            'validFrom' => '',
//            'validTo' => ''
//        ];




        try {
            $sd = new \CPSignedData();
            $sd->set_ContentEncoding(\BASE64_TO_BINARY);
            $sd->set_Content(base64_encode($data));
            $sd->VerifyCades($sign, \CADES_BES, 1);
            $signObject = $sd->get_Signers();
            $sObj = $signObject->get_Item(1);
            $cert = $sObj->get_Certificate();
            $subjectName = $cert->get_SubjectName();
            $issuerName = $cert->get_IssuerName();
            $Thumbprint = $cert->get_Thumbprint();
            $validFrom = $cert->get_ValidFromDate();
            $validTo = $cert->get_ValidToDate();

            return [
                'thumbprint' => $Thumbprint,
                'issuerName' => $issuerName,
                'subjectName' => $subjectName,
                'validFrom' => $validFrom,
                'validTo' => $validTo
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
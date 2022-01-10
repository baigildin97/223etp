<?php
declare(strict_types=1);
namespace App\Controller;


use App\Model\Flusher;
use App\Model\Front\Entity\OldProcedure\Document\Id;
use App\Model\Front\Entity\OldProcedure\OldProcedureRepository;
use App\Model\User\Service\PasswordHashGenerator;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    private $flusher;

    private $procedureRepository;


    public function __construct(Flusher $flusher, OldProcedureRepository $procedureRepository)
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
    }

    /**
     * @return Response
     * @Route("/home", name="home")
     */
    public function home(): Response {
        return $this->render('app/home.html.twig');
    }

    /**
     * @param Client $client
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @Route("/cp/test", name="cp.test")
     */
    public function test(PasswordHashGenerator $passwordHashGenerator): Response {
        // $2y$10$.PZK8BE7o6Lwga2AxdLuue3ob8.GJj48MLxivroE27un/MIxQkd6y
        // business-style@bk.ru
        // ============================================================
        //
        dd($passwordHashGenerator->hash('123456'));
        //
//        $client = new Client();
//        $req = $client->get('https://test.229etp.ru/api/procedures');
//        $res = json_decode($req->getBody()->getContents(), true);
//
//        foreach ($res['data'] as $re){
//            $procedure = $this->procedureRepository->getIdNumber($re['number']);
//
//            $procedure->getDocuments();
//
//            foreach ($re['documents'] as $r){
//                $procedure->addDocument(Id::next(),'1', $r['url'],$r['name']);
//            }
//        }

    }


    /**
     * @Route("/crypt/test", name="/crypt/test")
     */
    public function testcrypto(){
            $sign = "MIIM3wYJKoZIhvcNAQcCoIIM0DCCDMwCAQExDjAMBggqhQMHAQECAgUAMAsGCSqGSIb3DQEHAaCC
CPMwggjvMIIImqADAgECAhAB1mx1J8jB0AAAAAcsSwACMAwGCCqFAwcBAQMCBQAwggEqMRgwFgYF
KoUDZAESDTEwODc3NDY4MDYzMTExSDBGBgNVBAkMP9Co0L7RgdGB0LUg0K3QvdGC0YPQt9C40LDR
gdGC0L7QsiDQtC4gNTYg0YHRgtGALjMyINC+0YTQuNGBIDIxNDEaMBgGCCqFAwOBAwEBEgwwMDc3
MjA2MjMzNzkxCzAJBgNVBAYTAlJVMRkwFwYDVQQHDBDQsy4g0JzQvtGB0LrQstCwMRwwGgYDVQQI
DBM3NyDQsy4g0JzQvtGB0LrQstCwMRwwGgYJKoZIhvcNAQkBFg1jYUBhc3RyYWxtLnJ1MSEwHwYD
VQQKDBjQntCe0J4gItCQ0KHQotCg0JDQmy3QnCIxITAfBgNVBAMMGNCe0J7QniAi0JDQodCi0KDQ
kNCbLdCcIjAeFw0yMDA4MDcwNDQyMzFaFw0yMTA4MDcwNDQyMDBaMIIByDEYMBYGBSqFA2QBEg0x
MTUwMjgwMDcyMDQwMSIwIAYJKoZIhvcNAQkBFhNnYWx5cmluYXRAZ21haWwuY29tMRowGAYIKoUD
A4EDAQESDDAwMDI3NTkwNDM0NjELMAkGA1UEBhMCUlUxEjAQBgNVBAcMCdCj0YTQsCDQszE5MDcG
A1UECAwwMDIg0KDQtdGB0L/Rg9Cx0LvQuNC60LAg0JHQsNGI0LrQvtGA0YLQvtGB0YLQsNC9MT8w
PQYDVQQJDDY1MC3Qm9C10YLQuNGPINCe0LrRgtGP0LHRgNGPINGD0LssINC0LiAxMS8yLCDQvtGE
LiAzMTYxHTAbBgNVBAoMFNCe0J7QniAi0JXQotCfINCg0JEiMR0wGwYDVQQDDBTQntCe0J4gItCV
0KLQnyDQoNCRIjEfMB0GA1UEBAwW0JPQsNC70Y/Rg9GC0LTQuNC90L7QsjEmMCQGA1UEKgwd0KDQ
uNC90LDRgiDQoNCw0LzQuNC70LXQstC40YcxMDAuBgNVBAwMJ9CT0LXQvdC10YDQsNC70YzQvdGL
0Lkg0LTQuNGA0LXQutGC0L7RgDEWMBQGBSqFA2QDEgsxNDU3NTM2MTg4MjBmMB8GCCqFAwcBAQEB
MBMGByqFAwICJAAGCCqFAwcBAQICA0MABECMN39MFAPOL0V1eglEjRfUggsfzxuTGYBJ/AwrnPPR
GgfRjDHUZTbQf2UdiQ3MJk5lUeYcwJq12QR4lnIay+zDgQkAMkM0QjAwMDKjggTkMIIE4DAOBgNV
HQ8BAf8EBAMCBPAwHwYJKwYBBAGCNxUHBBIwEAYIKoUDAgIuAAgCAQECAQAwHQYDVR0lBBYwFAYI
KwYBBQUHAwIGCCsGAQUFBwMEMBkGCSqGSIb3DQEJDwQMMAowCAYGKoUDAgIVMB0GA1UdIAQWMBQw
CAYGKoUDZHEBMAgGBiqFA2RxAjA2BgUqhQNkbwQtDCsi0JrRgNC40L/RgtC+0J/RgNC+IENTUCIg
KNCy0LXRgNGB0LjRjyA0LjApMB0GA1UdDgQWBBRqWLnOdzUU7ilvJSC20hwl7DcO5zAMBgNVHRMB
Af8EAjAAMIIBZwYFKoUDZHAEggFcMIIBWAyBjiLQodGA0LXQtNGB0YLQstC+INC60YDQuNC/0YLQ
vtCz0YDQsNGE0LjRh9C10YHQutC+0Lkg0LfQsNGJ0LjRgtGLINC40L3RhNC+0YDQvNCw0YbQuNC4
IFZpUE5ldCBDU1AgNC4yIiAo0LLQsNGA0LjQsNC90YIg0LjRgdC/0L7Qu9C90LXQvdC40Y8gMikM
bdCf0YDQvtCz0YDQsNC80LzQvdGL0Lkg0LrQvtC80L/Qu9C10LrRgSAiVmlQTmV0INCj0LTQvtGB
0YLQvtCy0LXRgNGP0Y7RidC40Lkg0YbQtdC90YLRgCA0ICjQstC10YDRgdC40Y8gNC42KSIMJ9Ch
0KQvMTI0LTM0MzMg0L7RgiAwNiDQuNGO0LvRjyAyMDE4INCzLgwt0KHQpC8xMTgtMzUxMCDQvtGC
IDI1INC+0LrRgtGP0LHRgNGPIDIwMTgg0LMuMIGSBggrBgEFBQcBAQSBhTCBgjA8BggrBgEFBQcw
AYYwaHR0cDovL29jc3Aua2V5ZGlzay5ydS9PQ1NQLTExMzM5LTIwMTluL09DU1Auc3JmMEIGCCsG
AQUFBzAChjZodHRwOi8vd3d3LmNhLmFzdHJhbG0ucnUvY3J0L2FzdHJhbG0tMjAxOS1nb3N0MjAx
Mi5jZXIwgYsGA1UdHwSBgzCBgDA+oDygOoY4aHR0cDovL3d3dy5jYS5hc3RyYWxtLnJ1L2NkcC9h
bS9hc3RyYWwtbS0xMTMzOS0yMDE5bi5jcmwwPqA8oDqGOGh0dHA6Ly93d3cuZHAua2V5ZGlzay5y
dS9jZHAvYW0vYXN0cmFsLW0tMTEzMzktMjAxOW4uY3JsMIIBXwYDVR0jBIIBVjCCAVKAFK3waNwR
7UMRXh0/Un2P442FeDfVoYIBLKSCASgwggEkMR4wHAYJKoZIhvcNAQkBFg9kaXRAbWluc3Z5YXou
cnUxCzAJBgNVBAYTAlJVMRgwFgYDVQQIDA83NyDQnNC+0YHQutCy0LAxGTAXBgNVBAcMENCzLiDQ
nNC+0YHQutCy0LAxLjAsBgNVBAkMJdGD0LvQuNGG0LAg0KLQstC10YDRgdC60LDRjywg0LTQvtC8
IDcxLDAqBgNVBAoMI9Cc0LjQvdC60L7QvNGB0LLRj9C30Ywg0KDQvtGB0YHQuNC4MRgwFgYFKoUD
ZAESDTEwNDc3MDIwMjY3MDExGjAYBggqhQMDgQMBARIMMDA3NzEwNDc0Mzc1MSwwKgYDVQQDDCPQ
nNC40L3QutC+0LzRgdCy0Y/Qt9GMINCg0L7RgdGB0LjQuIIKFfAP0AAAAAAChDAMBggqhQMHAQED
AgUAA0EA9i8GNXS9KXkRiApRrLu3DsTdSv1VtYckMD0SW+P5GE54KWSCy5DZsvBbI42rVSX+CF9K
s4vnBWobtRJPury2xDGCA7EwggOtAgEBMIIBQDCCASoxGDAWBgUqhQNkARINMTA4Nzc0NjgwNjMx
MTFIMEYGA1UECQw/0KjQvtGB0YHQtSDQrdC90YLRg9C30LjQsNGB0YLQvtCyINC0LiA1NiDRgdGC
0YAuMzIg0L7RhNC40YEgMjE0MRowGAYIKoUDA4EDAQESDDAwNzcyMDYyMzM3OTELMAkGA1UEBhMC
UlUxGTAXBgNVBAcMENCzLiDQnNC+0YHQutCy0LAxHDAaBgNVBAgMEzc3INCzLiDQnNC+0YHQutCy
0LAxHDAaBgkqhkiG9w0BCQEWDWNhQGFzdHJhbG0ucnUxITAfBgNVBAoMGNCe0J7QniAi0JDQodCi
0KDQkNCbLdCcIjEhMB8GA1UEAwwY0J7QntCeICLQkNCh0KLQoNCQ0Jst0JwiAhAB1mx1J8jB0AAA
AAcsSwACMAwGCCqFAwcBAQICBQCgggIEMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZI
hvcNAQkFMQ8XDTIxMDQyMDA4MDUyOVowLwYJKoZIhvcNAQkEMSIEIBSyogP0UNB6JEwD2lF4AVJD
kQQzPYcJ577LqZLfw/L6MIIBlwYLKoZIhvcNAQkQAi8xggGGMIIBgjCCAX4wggF6MAoGCCqFAwcB
AQICBCCGjj7xyuuCOEFafpuFq4gaFZtg0c7kmv2UxJgyFNf6yjCCAUgwggEypIIBLjCCASoxGDAW
BgUqhQNkARINMTA4Nzc0NjgwNjMxMTFIMEYGA1UECQw/0KjQvtGB0YHQtSDQrdC90YLRg9C30LjQ
sNGB0YLQvtCyINC0LiA1NiDRgdGC0YAuMzIg0L7RhNC40YEgMjE0MRowGAYIKoUDA4EDAQESDDAw
NzcyMDYyMzM3OTELMAkGA1UEBhMCUlUxGTAXBgNVBAcMENCzLiDQnNC+0YHQutCy0LAxHDAaBgNV
BAgMEzc3INCzLiDQnNC+0YHQutCy0LAxHDAaBgkqhkiG9w0BCQEWDWNhQGFzdHJhbG0ucnUxITAf
BgNVBAoMGNCe0J7QniAi0JDQodCi0KDQkNCbLdCcIjEhMB8GA1UEAwwY0J7QntCeICLQkNCh0KLQ
oNCQ0Jst0JwiAhAB1mx1J8jB0AAAAAcsSwACMAwGCCqFAwcBAQEBBQAEQPa+knPgKHuppZjUk+rX
tSfQxPxCqoXTwQ0Ul29OE55vRDds3sUW4gb0mFdKuYI2uvbpOiH5lt+vk3NNpdeKnCs=";
            $hash = "c67c5305141bc0d34288b607b85cedb177e647b4d68d3647b6503d6bbb7d21f8";
        $sd = new \CPSignedData();
        $sd->set_ContentEncoding(\BASE64_TO_BINARY);
        $sd->set_Content(base64_encode($hash));
        $sd->VerifyCades($sign, \CADES_BES, 1);
        $signObject = $sd->get_Signers();
        $sObj = $signObject->get_Item(1);
        $cert = $sObj->get_Certificate();
        $subjectName = $cert->get_SubjectName();
        $Thumbprint = $cert->get_Thumbprint();

        dd($Thumbprint);
    }
}

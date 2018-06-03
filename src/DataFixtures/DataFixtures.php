<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Location;
use App\Entity\Vehicle;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class DataFixtures extends Fixture
{
    protected $coordinates;
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create('es_ES');
        $this->coordinates = [
          '40.385858,-3.719119',
          '40.385229,-3.720782',
          '40.385294,-3.710815',
          '40.393834,-3.709110',
        ];
    }
    public function load(ObjectManager $manager)
    {
        $totalLocations = count($this->coordinates);

        for ($i = 0; $i < $totalLocations; $i++) {
            $user = new User();
            $userLocation = new Location();
            $userVehicle = new Vehicle();

            $userVehicle->setType('coche');
            $userVehicle->setSeats(2);
            $userVehicle->setModel('Seat');
            $userVehicle->setPrice(5);

            $user->setUsername($this->faker->userName);
            $user->setPassword($this->faker->password);
            $user->setEmail($this->faker->email);
            $user->setCampus($userLocation);
            $user->setVehicle($userVehicle);
            $user->setOrigin($userLocation);

            $user->setTimeFlex($this->faker->randomDigit);
            $user->setLocationFlex($this->faker->numberBetween(20, 2000));
            $manager->persist($user);

            $latLong = explode(',', $this->coordinates[$i]);
            $latitude = $latLong[0];
            $longitude = $latLong[1];

            $formated_address = $this->getFormatedAddress($this->getAddress($latitude, $longitude));


            $userLocation->setLat($latitude);
            $userLocation->setLon($longitude);
            $userLocation->setlocationName($formated_address);
            $userLocation->setisCampus(0);
            $manager->persist($userLocation);
        }

        $manager->flush();
    }

    private function getFormatedAddress($address)
    {
        $decodedAddress = json_decode($address);
        return $decodedAddress->results[0]->formatted_address;
    }

    private function getAddress($lat, $lng)
    {
        if (!empty($lat) && !empty($lng)) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
           CURLOPT_URL => "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&key=AIzaSyBzJwyAkGdtgZ24vrzocFuY7pihOqOm66E",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 30,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
           CURLOPT_HTTPHEADER => array(
             "Cache-Control: no-cache",
           ),
         ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $this->faker->streetAddress;
            } else {
                return $response;
            }
        }
    }
}
